
<?php

trait P1PioExpectedTestData
{
    private function getExpectedToSendMessageObject()
    {
        return (object)[
            "address" => "https://ror.org/xxxxxxxx",
            "name" => "Science Publisher"
        ];
    }

    private function getExpectedFromMessageObject()
    {
        return (object)[
            "address" => "https://ror.org/04dkp9463",
            "name" => "University of Amsterdam"
        ];
    }

    private function getExpectedAuthorsArray()
    {
        $authors = [
            (object)[
                'listingorder' => 1,
                'listingorderAtAcceptance' => 1,
                'listingorderAtSubmission' => 1,
                'lastName' => 'Baggins',
                'firstName' => 'Frodo',
                'initials' => 'FB',
                'ORCID' => '0000-0000-0000-0000',
                'creditroles' => ['writing', 'visualization'],
                'isCorrespondingAuthor' => true,
                'isCorrespondingAuthorAtAcceptance' => true,
                'isCorrespondingAuthorAtSubmission' => true,
                'collaboration' => 'Laboratory collaboration',
                'institutions' => [
                    (object)[
                        'sourceaffiliation' => 'University of Amsterdam, Department Computer Science',
                        'name' => 'University of Amsterdam',
                        'ror' => 'https://ror.org/04dkp9463',
                        'isni' => '0000 0000 8499 2262',
                        'country' => 'NL'
                    ]
                ],
                'currentaddress' => [
                    (object)[
                        'name' => 'University of Amsterdam',
                        'ror' => 'https://ror.org/04dkp9463',
                        'isni' => '0000 0000 8499 2262'
                    ]
                ],
                'affiliation' => 'University of Amsterdam'
            ]
        ];
        return $authors;
    }

    private function getExpectedArticleObject()
    {
        return (object)[
                'title' => 'The International relations of Middle-Earth',
                'doi' => 'https://doi.org/00.0000/mearth.0000',
                'submissionId' => '00.0000',
                'type' => 'research-article',
                'funders' => [
                    (object)[
                        'name' => 'Aragorn Foundation',
                        'ror' => 'https://ror.org/999999',
                        'fundref' => '501100000000'
                    ],
                    (object)[
                        'name' => 'Middle-Earth Thinktank',
                        'ror' => 'https://ror.org/888888',
                        'fundref' => '501100000001'
                    ]
                ],
                'acknowledgement' => 'Aragorn Foundation, Middle-Earth Thinktank',
                'grants' => [
                    (object)[
                        'name' => 'Generous grant',
                        'id' => 'GD-000-001',
                        'doi' => 'https://doi.org/00.0000/00-2020-000000'
                    ]
                ],
                'manuscript' => (object)[
                    'dates' => (object)[
                        'submission' => '2021-02-01',
                        'acceptance' => '2021-03-01',
                        'publication' => '2021-04-01'
                    ],
                    'id' => '00.0000-000'
                ],
                'preprint' => (object)[
                    'title' => 'The International relations of Middle-Earth',
                    'url' => 'https://arxiv.org/00.0000-000',
                    'id' => '00.0000-000'
                ],
                'vor' => (object)[
                    'publication' => 'pure OA journal',
                    'license' => 'CC BY',
                    'deposition' => 'open repository, like PMC',
                    'researchdata' => 'data available on request',
                    'startdate' => '2024-01-01'
                ]
            ];
    }

    private function getExpectedJournalArray()
    {
        $journal = (object)[
            'name' => 'Middle Earth papers',
            'id' => '0000-0001',
            'issn' => '0000-0001',
            'eissn' => '0000-0002',
            'inDOAJ' => true,
            'typecomment' => 'Diamond'
        ];
        return $journal;
    }
}