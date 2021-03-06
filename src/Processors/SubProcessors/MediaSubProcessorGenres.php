<?php
class MediaSubProcessorGenres extends MediaSubProcessor
{
    public function process(array $documents, &$context)
    {
        $document = $documents[self::URL_MEDIA];
        
        $dom = self::getDOM($document);
        
        $xpath = new DOMXPath($dom);
        
        Database::delete(
            'mediagenre',
            [
                'media_id' => $context->media->id
            ]
        );
        
        $data = [];
        
        foreach ($xpath->query('//span[@itemprop = \'genre\']//a') as $node) {
            preg_match('/=([0-9]+)/', $node->getAttribute('href'), $matches);
            
            $genreIdMal = Strings::makeInteger($matches[1]);
            
            $genreName = Strings::removeSpaces($node->textContent);
            
            $data[] = [
                'media_id' => $context->media->id,
                'mal_id' => $genreIdMal,
                'name' => $genreName
            ];
        }
        
        foreach ($xpath->query('//span[starts-with(text(), \'Genres\')]/../a') as $node) {
            preg_match('/=([0-9]+)/', $node->getAttribute('href'), $matches);
            
            $genreIdMal = Strings::makeInteger($matches[1]);
            
            $genreName = Strings::removeSpaces($node->textContent);
            
            $data[] = [
                'media_id' => $context->media->id,
                'mal_id' => $genreIdMal,
                'name' => $genreName
            ];
        }
        
        Database::insert('mediagenre', $data);
    }
}
