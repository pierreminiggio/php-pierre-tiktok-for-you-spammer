<?php

namespace App;

use Dailymotion;
use DateTime;
use Exception;
use PierreMiniggio\DailymotionFileUploader\FileUploader;
use PierreMiniggio\DailymotionTokenProvider\AccessTokenProvider;
use PierreMiniggio\DailymotionUploadUrlMaker\UploadUrlMaker;
use PierreMiniggio\GithubActionRemotionRenderer\GithubActionRemotionRenderer;
use PierreMiniggio\GithubActionRunStarterAndArtifactDownloader\GithubActionRunStarterAndArtifactDownloader;
use PierreMiniggio\GithubActionRunStarterAndArtifactDownloader\GithubActionRunStarterAndArtifactDownloaderFactory;
use PierreMiniggio\GoogleTokenRefresher\GoogleClient;
use PierreMiniggio\HeropostAndYoutubeAPIBasedVideoPoster\Video;
use PierreMiniggio\HeropostAndYoutubeAPIBasedVideoPoster\VideoPosterFactory;
use PierreMiniggio\HeropostYoutubePosting\YoutubeCategoriesEnum;
use PierreMiniggio\HeropostYoutubePosting\YoutubeVideo;

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
        $token = $config['apiToken'];

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
                1800
            );
        } catch (Exception $e) {
            echo PHP_EOL . 'Error while rendering : ' . $e->getMessage();
            var_dump($e->getTrace());
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
