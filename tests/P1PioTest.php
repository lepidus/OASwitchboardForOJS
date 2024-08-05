<?php

import('lib.pkp.tests.PKPTestCase');
import('plugins.generic.OASwitchboard.classes.messages.P1Pio');
import('plugins.generic.OASwitchboard.tests.helpers.P1PioExpectedTestData');
import('plugins.generic.OASwitchboard.tests.helpers.ObjectFactory');

class P1PioTest extends PKPTestCase
{
    use P1PioExpectedTestData;

    private $P1Pio;
    private $submission;

    protected function setUp(): void
    {
        parent::setUp();
        $journal = ObjectFactory::createMockedJournal($this, $issn = "0000-0001");
        $this->submission = ObjectFactory::createTestSubmission($journal, true);
        $this->P1Pio = new P1Pio($this->submission);
    }

    protected function getMockedDAOs()
    {
        return [
            'JournalDAO'
        ];
    }

    public function testGetAuthorGivenName()
    {
        $authorsData = $this->P1Pio->getAuthorsData();
        $authorGivenName = $authorsData[0]['firstName'];
        $this->assertEquals($authorGivenName, 'Iris');
    }

    public function testGetAuthorLastName()
    {
        $authorsData = $this->P1Pio->getAuthorsData();
        $authorFamilyName = $authorsData[0]['lastName'];
        $this->assertEquals($authorFamilyName, 'Castanheiras');
    }

    public function testAuthorInstitutionAddress()
    {
        $authorsData = $this->P1Pio->getAuthorsData();
        $institution = $authorsData[0]['institutions'][0];
        $this->assertTrue(is_array($institution));
        $this->assertEquals($institution['ror'], 'https://ror.org/xxxxxxxxrecipient');
    }

    public function testGetAuthorInstitutionName()
    {
        $authorsData = $this->P1Pio->getAuthorsData();
        $institution = $authorsData[0]['institutions'][0];
        $this->assertTrue(is_array($institution));
        $this->assertEquals($institution['name'], 'Lepidus Tecnologia');
    }

    public function testGetAuthorLocalizedAffiliation()
    {
        $authorsData = $this->P1Pio->getAuthorsData();
        $affiliation = $authorsData[0]['affiliation'];
        $this->assertEquals($affiliation, 'Lepidus Tecnologia');
    }

    public function testGetAuthorOrcid()
    {
        $authorsData = $this->P1Pio->getAuthorsData();
        $orcid = $authorsData[0]['orcid'];
        $this->assertEquals($orcid, 'https://orcid.org/0000-0000-0000-0000');
    }

    public function testGetAuthorEmail()
    {
        $authorsData = $this->P1Pio->getAuthorsData();
        $email = $authorsData[0]['email'];
        $this->assertEquals($email, 'castanheirasiris@lepidus.com.br');
    }

    public function testGetIsCorrespondingAuthor()
    {
        $authorsData = $this->P1Pio->getAuthorsData();
        $isCorrespondingAuthor = $authorsData[1]['isCorrespondingAuthor'];
        $this->assertTrue($isCorrespondingAuthor);
        $isNotCorrespondingAuthor = $authorsData[0]['isCorrespondingAuthor'];
        $this->assertFalse($isNotCorrespondingAuthor);
    }

    public function testGetArticleTitle()
    {
        $articleData = $this->P1Pio->getArticleData();
        $title = $articleData['title'];
        $this->assertEquals($title, 'The International relations of Middle-Earth');
    }

    public function testGetArticleDoi()
    {
        $articleData = $this->P1Pio->getArticleData();
        $doi = $articleData['doi'];
        $this->assertEquals($doi, 'https://doi.org/00.0000/mearth.0000');
    }

    public function testGetArticleType()
    {
        $articleData = $this->P1Pio->getArticleData();
        $type = $articleData['type'];
        $this->assertEquals($type, 'research-article');
    }

    public function testGetArticleVor()
    {
        $articleData = $this->P1Pio->getArticleData();
        $vor = $articleData['vor'];
        $this->assertEquals('pure OA journal', $vor['publication']);
        $this->assertEquals('CC BY-NC-ND', $vor['license']);
    }

    public function testGetJournalName()
    {
        $journalData = $this->P1Pio->getJournalData();
        $this->assertEquals('Middle Earth papers', $journalData['name']);
        $this->assertEquals('0000-0001', $journalData['id']);
    }

    public function testP1PioMessageHeader()
    {
        $header = $this->P1Pio->getContent()['header'];
        $this->assertEquals('p1', $header['type']);
        $this->assertEquals('v2', $header['version']);
        $this->assertEquals($this->getExpectedRecipient(), $header['to']);
        $this->assertEquals(true, $header['persistent']);
        $this->assertEquals(true, $header['pio']);
    }

    public function testP1PioMessageData()
    {
        $data = $this->P1Pio->getContent()['data'];
        $this->assertEquals('VoR', $data['timing']);
        $this->assertEquals($this->getExpectedAuthorsArray(), $data['authors']);
        $this->assertEquals($this->getExpectedArticleObject(), $data['article']);
        $this->assertEquals($this->getExpectedJournalArray(), $data['journal']);
    }

    public function testValidateHasMinimumSubmissionDataReturnsEmptyIfAllMandatoryDataIsPassed()
    {
        $this->assertTrue(empty($this->P1Pio->validateHasMinimumSubmissionData()));
    }

    public function testValidateHasMinimumSubmissionDataShouldReturnMessageIfAuthorDoesNotHaveFamilyName()
    {
        $firstAuthor = $this->submission->getAuthors()[0];
        $firstAuthor->setData('familyName', null);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            "##plugins.generic.OASwitchboard.postRequirementsError##"
        );
        $P1Pio = new P1Pio($this->submission);
    }

    public function testValidateHasMinimumSubmissionDataShouldReturnMessagesIfAuthorDoesNotHaveAffiliation()
    {
        $firstAuthor = $this->submission->getAuthors()[0];
        $firstAuthor->setData('affiliation', null);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            "##plugins.generic.OASwitchboard.postRequirementsError##"
        );
        $P1Pio = new P1Pio($this->submission);
    }

    public function testValidateHasMinimumSubmissionDataShouldReturnMessagesIfArticleDoesNotHaveDOIAssociated()
    {
        $publication = $this->submission->getCurrentPublication();
        $publication->setData('pub-id::doi', null);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            "##plugins.generic.OASwitchboard.postRequirementsError##"
        );
        $P1Pio = new P1Pio($this->submission);
    }

    public function testValidateHasMinimumSubmissionDataShouldReturnMessagesIfArticleDoesNotHaveISSNAssociated()
    {
        $journal = ObjectFactory::createMockedJournal($this);
        $submission = ObjectFactory::createTestSubmission($journal);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            "##plugins.generic.OASwitchboard.postRequirementsError##"
        );
        $P1Pio = new P1Pio($submission);
    }
}
