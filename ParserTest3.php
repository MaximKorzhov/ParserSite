<?php

class ParserHTML
{
    public function getPaths($path,$pathsPattern)
    {
        $pageContent = file_get_contents('https://book24.ru' . $path);
        preg_match_all($pathsPattern, $pageContent, $paths);
        return array_unique($paths[0] ?? []);
    }

    public function getDataFromString($patterns, $pageContent)
    {
        foreach ($patterns as $pattern)
        {
            preg_match_all($pattern, $pageContent, $arrayData);

            $rowData[] = $arrayData[1][0] ?? strip_tags($arrayData[0][0]);
        }
        return $rowData ?? [];
    }

    public function getDataFromBlock($pageContent, $blockPattern, $splitPattern, $searchPatterns)
    {
        $dataOut = [];

        preg_match_all($blockPattern, $pageContent, $blockContent);
        $arrayStrings = preg_split($splitPattern, preg_replace(['/\n/', '/\s{2,}/', '#&nbsp;#'], ' ', $blockContent[0][0]));

        foreach ($arrayStrings as $arrayString)
        {
            $stringData = strip_tags($arrayString);

            foreach ($searchPatterns as $searchPattern)
            {
                if (preg_match($searchPattern, $stringData))
                {
                    $dataOut[] = trim($stringData, " ");
                }
            }
        }
        return $dataOut;
    }

    public function write($file, $data)
    {
        $separated = is_array($data) ? implode("\n", $data) : $data;

        file_put_contents($file, $separated, FILE_APPEND | LOCK_EX);
        return '1';
    }


    public function getPages($html, $pagesPattern)
    {
        preg_match_all($pagesPattern, $html, $pages);
        return array_unique($pages[0] ?? []);
    }
}