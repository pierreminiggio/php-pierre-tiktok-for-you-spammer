<?php

namespace App;

use Exception;
use PierreMiniggio\GithubActionRunStarterAndArtifactDownloader\GithubActionRunStarterAndArtifactDownloaderFactory;

class App
{

    public function run(): void
    {

        $projectFolder =
            __DIR__
            . DIRECTORY_SEPARATOR
            . '..'
            . DIRECTORY_SEPARATOR
        ;
        $config = require
            $projectFolder
            . 'config.php'
        ;

        $runnerAndDownloader = (new GithubActionRunStarterAndArtifactDownloaderFactory())->make();
        $runnerAndDownloader->sleepTimeBetweenRunCreationChecks = 30;
        $runnerAndDownloader->numberOfRunCreationChecksBeforeAssumingItsNotCreated = 20;

        $spammerProjects = $config['spammerProjects'];
        $spammerProject = $spammerProjects[array_rand($spammerProjects)];

        echo 'Starting action ...';

        try {
            $artifacts = $runnerAndDownloader->runActionAndGetArtifacts(
                $spammerProject['token'],
                $spammerProject['account'],
                $spammerProject['project'],
                'spam.yml',
                1350
            );
        } catch (Exception $e) {
            echo PHP_EOL . 'Error while rendering : ' . $e->getMessage();
            var_dump($e->getTrace());

            return;
        }

        echo ' Done !' . PHP_EOL;

        foreach ($artifacts as $artifact) {

            echo PHP_EOL . 'Cleaning artifact ' . $artifact . ' ...';

            if (file_exists($artifact)) {
                unlink($artifact);
            }

            echo ' Cleaned !';
        }
    }
}
