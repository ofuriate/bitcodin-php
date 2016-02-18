<?php
/**
 * Created by David Moser <david.moser@bitmovin.net>
 * Date: 25.01.16
 * Time: 14:33
 */

namespace test\job;


use bitcodin\AudioMetaData;
use bitcodin\Bitcodin;
use bitcodin\HttpInputConfig;
use bitcodin\Input;
use bitcodin\Job;
use bitcodin\JobConfig;
use bitcodin\JobSpeedTypes;
use bitcodin\ManifestTypes;

class JobSkippedAnalysisTest extends AbstractJobTest
{
    public function __construct()
    {
        parent::__construct();

        Bitcodin::setApiToken($this->getApiKey());
    }

    public function testMultiLanguageJob()
    {

        $inputConfig = new HttpInputConfig();
        $inputConfig->url = self::URL_FILE;
        $inputConfig->skipAnalysis = true;

        $input = Input::create($inputConfig);

        $audioMetaDataJustSound = new AudioMetaData();
        $audioMetaDataJustSound->defaultStreamId = 0;
        $audioMetaDataJustSound->label = 'Just Sound';
        $audioMetaDataJustSound->language = 'de';

        $audioMetaDataSoundAndVoice = new AudioMetaData();
        $audioMetaDataSoundAndVoice->defaultStreamId = 1;
        $audioMetaDataSoundAndVoice->label = 'Sound and Voice';
        $audioMetaDataSoundAndVoice->language = 'en';

        /* CREATE ENCODING PROFILE */
        $encodingProfile = $this->getMultiLanguageEncodingProfile();

        $jobConfig = new JobConfig();
        $jobConfig->encodingProfile = $encodingProfile;
        $jobConfig->input = $input;
        $jobConfig->manifestTypes[] = ManifestTypes::MPD;
        $jobConfig->manifestTypes[] = ManifestTypes::M3U8;
        $jobConfig->speed = JobSpeedTypes::STANDARD;
        $jobConfig->audioMetaData[] = $audioMetaDataJustSound;
        $jobConfig->audioMetaData[] = $audioMetaDataSoundAndVoice;

        /* CREATE JOB */
        $job = Job::create($jobConfig);

        $this->assertInstanceOf('bitcodin\Job', $job);
        $this->assertNotNull($job->jobId);
        $this->assertNotEquals($job->status, Job::STATUS_ERROR);

        return $job;
    }
}
