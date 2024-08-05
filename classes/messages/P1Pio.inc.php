<?php

import('plugins.generic.OASwitchboard.classes.messages.P1PioDataFormat');
import('plugins.generic.OASwitchboard.classes.messages.LicenseAcronym');
import('plugins.generic.OASwitchboard.classes.exceptions.P1PioException');
import('classes.submission.Submission');
import('lib.pkp.classes.log.SubmissionLog');

class P1Pio
{
    use P1PioDataFormat;
    use LicenseAcronym;
    private $submission;
    private const ARTICLE_TYPE = 'research-article';
    private const DOI_BASE_URL = 'https://doi.org/';
    private const OPEN_ACCESS_POLICY = 'pure OA journal';

    public function __construct(Submission $submission)
    {
        $this->submission = $submission;
        $minimumData = $this->validateHasMinimumSubmissionData();
        if (!empty($minimumData)) {
            throw new P1PioException(__('plugins.generic.OASwitchboard.postRequirementsError'), 0, $minimumData);
        }
    }

    public function getAuthorsData(): array
    {
        $authors = $this->submission->getAuthors();
        $authorsData = [];
        foreach ($authors as $author) {
            $lastNameRetrieved = $author->getLocalizedFamilyName();
            $lastName = is_array($lastNameRetrieved) ? reset($lastNameRetrieved) : $lastNameRetrieved;
            $firstName = $author->getLocalizedGivenName();
            $affiliationName = $author->getLocalizedAffiliation();
            $orcid = $author->getOrcid();
            $email = $author->getEmail();

            $authorsData[] = [
                'lastName' => $lastName,
                'firstName' => $firstName,
                'affiliation' => (string)$affiliationName,
                'institutions' => [
                    [
                        'name' => (string)$affiliationName,
                        'ror' => (string)$author->getData('rorId')
                    ]
                ],
            ];

            $lastAuthorIndex = count($authorsData) - 1;
            if (!empty($orcid)) {
                $authorsData[$lastAuthorIndex]['orcid'] = $orcid;
            }

            if (!empty($email)) {
                $authorsData[$lastAuthorIndex]['email'] = $email;
            }

            $primaryContactId = $this->submission->getCurrentPublication()->getData('primaryContactId');
            $authorsData[$lastAuthorIndex]['isCorrespondingAuthor'] = $primaryContactId === $author->getId();

            $contributorSequence = $author->getData('seq') + 1;
            $authorsData[$lastAuthorIndex]['listingorder'] = $contributorSequence;
        }
        return $authorsData;
    }

    public function getArticleData(): array
    {
        $articleTitle = $this->submission->getLocalizedFullTitle();
        $publication = $this->submission->getCurrentPublication();
        $license = $this->submission->getLicenseUrl();
        $licenseAcronym = $this->getLicenseAcronym($license);
        $doi = $publication->getData('pub-id::doi') ?
            self::DOI_BASE_URL . $publication->getData('pub-id::doi') :
            "";
        $articleData = [
            'title' => $articleTitle,
            'doi' => $doi,
            'type' => self::ARTICLE_TYPE,
            'vor' => [
                'publication' => self::OPEN_ACCESS_POLICY,
                'license' => $licenseAcronym
            ],
            'submissionId' => (string) $this->submission->getId()
        ];
        $fileId = $this->getFileId();
        if ($fileId) {
            $articleData['manuscript']['id'] = (string) $fileId;
        }

        return $articleData;
    }

    private function getFileId()
    {
        $journal = DAORegistry::getDAO('JournalDAO')->getById($this->submission->getData('contextId'));
        $galleys = $this->getArticleTextGalleys();

        if (count($galleys) > 1) {
            return $this->getFirstPrimaryLocaleGalleyFileId($galleys, $journal->getPrimaryLocale());
        }

        return empty($galleys) ? null : $galleys[0]->getData('submissionFileId');
    }

    private function getFirstPrimaryLocaleGalleyFileId($galleys, $primaryLocale)
    {
        foreach ($galleys as $galley) {
            if ($galley->getData('locale') === $primaryLocale) {
                return $galley->getData('submissionFileId');
            }
        }
    }

    private function getArticleTextGenreId(): int
    {
        return DAORegistry::getDAO('GenreDAO')
            ->getByKey('SUBMISSION', $this->submission->getData('contextId'))
            ->getId();
    }

    private function getArticleTextGalleys(): array
    {
        $articleTextGalleys = [];
        foreach ($this->submission->getGalleys() as $galley) {
            $submissionFileId = $galley->getData('submissionFileId');
            $genreId = $this->getGenreIdOfSubmissionFile($submissionFileId);
            if ($genreId === $this->getArticleTextGenreId()) {
                $articleTextGalleys[] = $galley;
            }
        }
        return $articleTextGalleys;
    }

    public function getGenreIdOfSubmissionFile($submissionFileId)
    {
        return Services::get('submissionFile')->get($submissionFileId)->getData('genreId');
    }

    public function getJournalData(): array
    {
        $journalDao = DAORegistry::getDAO('JournalDAO');
        $journalId = $this->submission->getContextId();
        $journal = $journalDao->getById($journalId);

        $journalData = [
            'name' => $journal->getLocalizedName(),
            'id' => $this->chooseIssn($journal),
            'eissn' => $journal->getData('onlineIssn'),
            'issn' => $journal->getData('printIssn')
        ];
        return $journalData;
    }

    private function chooseIssn($journal)
    {
        return $journal->getData('onlineIssn') ?: $journal->getData('printIssn') ?: null;
    }

    public function getContent(): array
    {
        return $this->assembleMessage();
    }

    public function validateHasMinimumSubmissionData(): array
    {
        $missingDataMessages = [];

        $message = $this->getContent();
        $header = $message['header'];
        $data = $message['data'];

        foreach ($data['authors'] as $key => $author) {
            if (empty($author['lastName'])) {
                $missingDataMessages[] = 'plugins.generic.OASwitchboard.postRequirementsError.familyName';
            }
            if (empty($author['affiliation'])) {
                $missingDataMessages[] = 'plugins.generic.OASwitchboard.postRequirementsError.affiliation';
            }
        }
        if (empty($data['article']['doi'])) {
            $missingDataMessages[] = 'plugins.generic.OASwitchboard.postRequirementsError.doi';
        }
        if (empty($data['journal']['id'])) {
            $missingDataMessages[] = 'plugins.generic.OASwitchboard.postRequirementsError.issn';
        }

        return $missingDataMessages;
    }
}
