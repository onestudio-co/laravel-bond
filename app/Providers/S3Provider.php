<?php

namespace App\Providers;

use Aws\S3\BatchDelete;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Illuminate\Support\Collection;
use Publiux\laravelcdn\Contracts\CdnHelperInterface;
use Publiux\laravelcdn\Providers\AwsS3Provider;
use Publiux\laravelcdn\Providers\Contracts\ProviderInterface;
use Publiux\laravelcdn\Providers\Provider;
use Publiux\laravelcdn\Validators\Contracts\ProviderValidatorInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

class S3Provider extends Provider implements ProviderInterface
{
    /**
     * All the configurations needed by this class with the
     * optional configurations default values.
     *
     * @var array
     */
    protected $default = [
        'url' => null,
        'threshold' => 10,
        'providers' => [
            'aws' => [
                's3' => [
                    'version' => null,
                    'region' => null,
                    'endpoint' => null,
                    'buckets' => null,
                    'upload_folder' => '',
                    'http' => null,
                    'acl' => 'public-read',
                    'cloudfront' => [
                        'use' => false,
                        'cdn_url' => null,
                    ],
                ],
            ],
        ],
    ];

    /**
     * Required configurations (must exist in the config file).
     *
     * @var array
     */
    protected $rules = ['version', 'region', 'key', 'secret', 'buckets', 'url'];

    /**
     * this array holds the parsed configuration to be used across the class.
     *
     * @var Array
     */
    protected $supplier;

    /**
     * @var Instance of Aws\S3\S3Client
     */
    protected $s3_client;

    /**
     * @var Instance of Guzzle\Batch\BatchBuilder
     */
    protected $batch;

    /**
     * @var \Publiux\laravelcdn\Contracts\CdnHelperInterface
     */
    protected $cdn_helper;

    /**
     * @var \Publiux\laravelcdn\Validators\Contracts\ConfigurationsInterface
     */
    protected $configurations;

    /**
     * @var \Publiux\laravelcdn\Validators\Contracts\ProviderValidatorInterface
     */
    protected $provider_validator;

    /**
     * @param \Symfony\Component\Console\Output\ConsoleOutput $console
     * @param \Publiux\laravelcdn\Validators\Contracts\ProviderValidatorInterface $provider_validator
     * @param \Publiux\laravelcdn\Contracts\CdnHelperInterface                    $cdn_helper
     */
    public function __construct(
        ConsoleOutput $console,
        ProviderValidatorInterface $provider_validator,
        CdnHelperInterface $cdn_helper
    ) {
//        throw new \Exception();
        $this->console = $console;
        $this->provider_validator = $provider_validator;
        $this->cdn_helper = $cdn_helper;
    }

    /**
     * Read the configuration and prepare an array with the relevant configurations
     * for the (AWS S3) provider. and return itself.
     *
     * @param $configurations
     *
     * @return $this
     */
    public function init($configurations)
    {
        // merge the received config array with the default configurations array to
        // fill missed keys with null or default values.
        $this->default = array_replace_recursive($this->default, $configurations);

        $supplier = [
            'provider_url' => $this->default['url'],
            'threshold' => $this->default['threshold'],
            'version' => $this->default['providers']['aws']['s3']['version'],
            'region' => $this->default['providers']['aws']['s3']['region'],
            'endpoint' => $this->default['providers']['aws']['s3']['endpoint'],
            'buckets' => $this->default['providers']['aws']['s3']['buckets'],
            'acl' => $this->default['providers']['aws']['s3']['acl'],
            'cloudfront' => $this->default['providers']['aws']['s3']['cloudfront']['use'],
            'cloudfront_url' => $this->default['providers']['aws']['s3']['cloudfront']['cdn_url'],
            'http' => $this->default['providers']['aws']['s3']['http'],
            'upload_folder' => $this->default['providers']['aws']['s3']['upload_folder']
        ];

        // check if any required configuration is missed
        $this->provider_validator->validate($supplier, $this->rules);

        $this->supplier = $supplier;

        return $this;
    }

    /**
     * Upload assets.
     *
     * @param $assets
     *
     * @return bool
     */
    public function upload($assets)
    {
        // connect before uploading
        $connected = $this->connect();

        if (!$connected) {
            return false;
        }

        // user terminal message
        $this->console->writeln('<fg=yellow>Comparing local files and bucket...</fg=yellow>');

        $assets = $this->getFilesAlreadyOnBucket($assets);

        // upload each asset file to the CDN
        if (count($assets) > 0) {
            $this->console->writeln('<fg=yellow>Upload in progress......</fg=yellow>');
            foreach ($assets as $file) {
                try {
                    $this->console->writeln('<fg=cyan>'.'Uploading file path: '.$file->getRealpath().'</fg=cyan>');
                    $command = $this->s3_client->getCommand('putObject', [

                        // the bucket name
                        'Bucket' => $this->getBucket(),
                        // the path of the file on the server (CDN)
                        'Key' => $this->supplier['upload_folder'] . str_replace('\\', '/', $file->getPathName()),
                        // the path of the path locally
                        'Body' => fopen($file->getRealPath(), 'r'),
                        // the permission of the file

                        'ACL' => $this->acl,
                        'CacheControl' => $this->default['providers']['aws']['s3']['cache-control'],
                        'Metadata' => $this->default['providers']['aws']['s3']['metadata'],
                        'Expires' => $this->default['providers']['aws']['s3']['expires'],
                    ]);


                    $this->s3_client->execute($command);
                } catch (S3Exception $e) {
                    $this->console->writeln('<fg=red>Upload error: '.$e->getMessage().'</fg=red>');
                    return false;
                }
            }

            // user terminal message
            $this->console->writeln('<fg=green>Upload completed successfully.</fg=green>');
        } else {
            // user terminal message
            $this->console->writeln('<fg=yellow>No new files to upload.</fg=yellow>');
        }

        return true;
    }

    /**
     * Create an S3 client instance
     * (Note: it will read the credentials form the .env file).
     *
     * @return bool
     */
    public function connect()
    {
        try {
            // Instantiate an S3 client
            $this->setS3Client(new S3Client([
                        'version' => $this->supplier['version'],
                        'region' => $this->supplier['region'],
                        'endpoint' => $this->supplier['endpoint'],
                        'http' => $this->supplier['http']
                    ]
                )
            );
        } catch (\Exception $e) {
            $this->console->writeln('<fg=red>Connection error: '.$e->getMessage().'</fg=red>');
            return false;
        }

        return true;
    }

    /**
     * @param $s3_client
     */
    public function setS3Client($s3_client)
    {
        $this->s3_client = $s3_client;
    }

    /**
     * @param  Collection $assets
     * @return mixed
     */
    private function getFilesAlreadyOnBucket($assets)
    {
        $filesOnAWS = new Collection([]);

        $params = ['Bucket' => $this->getBucket()];
        do {
            $files = $this->s3_client->listObjectsV2($params);
            $params['ContinuationToken'] = $files->get('NextContinuationToken');

            foreach ($files->get('Contents') ?? [] as $file) {
                $a = [
                    'Key' => $file['Key'],
                    "LastModified" => $file['LastModified']->getTimestamp(),
                    'Size' => intval($file['Size'])
                ];
                $filesOnAWS->put($file['Key'], $a);
            }
        } while ($files->get('IsTruncated'));

        if ($filesOnAWS->isEmpty()) {
            //no files on bucket. lets upload everything found.
            return $assets;
        }

        return $assets->filter(function ($file) use (&$filesOnAWS) {
            $fileOnAWS = $filesOnAWS->get(str_replace('\\', '/', $file->getPathName()));
            if(! $fileOnAWS){
                return true ;
            }
            //select to upload files that are different in size AND last modified time.
            return $file->getMTime() !== $fileOnAWS['LastModified'] && $file->getSize() !== $fileOnAWS['Size'];
        });
    }

    /**
     * @return array
     */
    public function getBucket()
    {
        // this step is very important, "always assign returned array from
        // magical function to a local variable if you need to modify it's
        // state or apply any php function on it." because the returned is
        // a copy of the original variable. this prevent this error:
        // Indirect modification of overloaded property
        // Vinelab\Cdn\Providers\AwsS3Provider::$buckets has no effect
        $bucket = $this->buckets;

        return rtrim(key($bucket), '/');
    }

    /**
     * Empty bucket.
     *
     * @return bool
     */
    public function emptyBucket()
    {

        // connect before uploading
        $connected = $this->connect();

        if (!$connected) {
            return false;
        }

        // user terminal message
        $this->console->writeln('<fg=yellow>Emptying in progress...</fg=yellow>');

        try {

            // Get the contents of the bucket for information purposes
            $contents = $this->s3_client->listObjects([
                'Bucket' => $this->getBucket(),
                'Key' => '',
            ]);

            // Check if the bucket is already empty
            if (!$contents['Contents']) {
                $this->console->writeln('<fg=green>The bucket '.$this->getBucket().' is already empty.</fg=green>');

                return true;
            }

            // Empty out the bucket
            $empty = BatchDelete::fromListObjects($this->s3_client, [
                'Bucket' => $this->getBucket(),
                'Prefix' => null,
            ]);

            $empty->delete();
        } catch (S3Exception $e) {
            $this->console->writeln('<fg=red>Deletion error: '.$e->getMessage().'</fg=red>');
            return false;
        }

        $this->console->writeln('<fg=green>The bucket '.$this->getBucket().' is now empty.</fg=green>');

        return true;
    }

    /**
     * This function will be called from the CdnFacade class when
     * someone use this {{ Cdn::asset('') }} facade helper.
     *
     * @param $path
     *
     * @return string
     */
    public function urlGenerator($path)
    {
        if ($this->getCloudFront() === true) {
            $url = $this->cdn_helper->parseUrl($this->getCloudFrontUrl());

            return $url['scheme'] . '://' . $url['host'] . '/' . $path;
        }

        $url = $this->cdn_helper->parseUrl($this->getUrl());

        $bucket = $this->getBucket();
        $bucket = (!empty($bucket)) ? $bucket.'.' : '';

        return $url['scheme'] . '://' . $bucket . $url['host'] . '/' . $path;
    }

    /**
     * @return string
     */
    public function getCloudFront()
    {
        if (!is_bool($cloudfront = $this->cloudfront)) {
            return false;
        }

        return $cloudfront;
    }

    /**
     * @return string
     */
    public function getCloudFrontUrl()
    {
        return rtrim($this->cloudfront_url, '/').'/';
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return rtrim($this->provider_url, '/') . '/';
    }


    public function __get($attr)
    {
        return isset($this->supplier[$attr]) ? $this->supplier[$attr] : null;
    }
}
