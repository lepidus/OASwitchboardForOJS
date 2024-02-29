<?php

trait P1PioExpectedTestData
{
    private function getExpectedToSendMessageObject()
    {
        return [
            "address" => "https://ror.org/xxxxxxxx",
            "name" => "Science Publisher"
        ];
    }

    private function getExpectedFromMessageObject()
    {
        return [
            "address" => "https://ror.org/04dkp9463",
            "name" => "University of Amsterdam"
        ];
    }

    private function getExpectedAuthorsArray()
    {
        $authors = [
            [
                'lastName' => 'Castanheiras',
                'firstName' => 'Iris',
                'affiliation' => 'Lepidus Tecnologia',
                'institutions' => [
                    [
                        'name' => 'Lepidus Tecnologia'
                    ]
                ]
            ]
        ];
        return $authors;
    }

    private function getExpectedArticleObject()
    {
        return [
                'title' => 'The International relations of Middle-Earth',
                'doi' => 'https://doi.org/00.0000/mearth.0000',
                'type' => 'research-article',
                'vor' => [
                    'publication' => 'pure OA journal',
                    'license' => 'CC BY-NC-ND'
                ]

            ];
    }

    private function getExpectedJournalArray()
    {
        $journal = [
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
