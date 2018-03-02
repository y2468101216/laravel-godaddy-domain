<?php
/**
 * Created by PhpStorm.
 * User: lucas_chang
 * Date: 2018/2/14
 * Time: 上午 10:37
 */

namespace Y2468101216\Godaddy\Console;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Config\Repository as Config;

class DomainCommand extends Command
{
    protected $recordType;
    protected $recordName;
    protected $recordValue;
    protected $domain;
    protected $config;
    protected $client;
    protected $clientOptions;
    const API_URL = "https://api.godaddy.com/v1";
    const SUCCESS_HTTP_CODE = 200;
    const DEFAULT_RECORD_TYPE = 'A';
    const DEFAULT_RECORD_NAME = 'www';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'godaddy-domain {--domain=} {--type=} {--name=} {--value=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'register a record from env to Godady in howinvest.com';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Client $client, Config $config)
    {
        $this->client = $client;

        $this->info('register domain to Godady start');

        $this->config = $this->getConfig($config);
        $this->clientOptions = $this->getClientOption();

        $this->recordType = $this->retrieveOption('type');
        $this->recordName = $this->retrieveOption('name');
        $this->recordValue = $this->retrieveOption('value');
        $this->domain = $this->retrieveOption('domain');

        $this->checkGodaddyStatus();

        $isRecordExist = $this->isExistSameRecord();

        $this->updateOrCreateRecord($isRecordExist);

        $this->info('register domain to Godady end');

        return 0;
    }

    protected function getClientOption() : array
    {
        $key = $this->config['key'];
        $secret = $this->config['secret'];

        return [
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'sso-key '.$key.':'.$secret,
                'Content-Type' => 'application/json'
            ]
        ];
    }

    protected function retrieveOption(string $optionName) : string
    {
        $result = $this->option($optionName);

        if (empty($result)) {
            $this->info('no retrive '.$optionName.', use default : '.$this->config[$optionName]);
            $result = $this->config[$optionName];
        }

        return $result;
    }

    protected function checkGodaddyStatus() : void
    {
        $url = self::API_URL.'/domains';

        $res = $this->client->get($url, $this->clientOptions);
        $httpCode = $res->getStatusCode();

        if ($httpCode != self::SUCCESS_HTTP_CODE) {
            $this->error('godaddy api is not alive, http code : '.$httpCode.', please check your key and secret');
            exit(1);
        }
    }

    protected function isExistSameRecord() : bool
    {
        $url = self::API_URL.'/domains/'.$this->domain.'/records/'.$this->recordType.'/'.$this->recordName;

        $res = $this->client->get($url, $this->clientOptions);
        $result = json_decode($res->getBody());
        $httpCode = $res->getStatusCode();

        if ($httpCode != 200) {
            $this->error('godaddy api error, cannot get, http status code : '.$httpCode);
            exit(1);
        }

        if (empty($result)) {
            return false;
        }

        return true;
    }

    protected function updateOrCreateRecord($isExist) : void
    {
        if($isExist) {
            $this->info('this record exists, use update record');
            $this->updateRecord();
            return;
        }

        $this->info('this record do not exist, use create record');
        $this->createRecord();

        return;
    }

    protected function updateRecord() : void
    {
        $url = self::API_URL.'/domains/'.$this->domain.'/records/'.$this->recordType.'/'.$this->recordName;
        $options = $this->clientOptions;
        $options['json'] = [
            [
                'data' => $this->recordValue
            ]
        ];

        $res = $this->client->put($url, $options);
        $httpCode = $res->getStatusCode();

        if ($httpCode != 200) {
            $this->error('cannot update record, http code : '.$httpCode);
            exit(1);
        }
        $this->info('update record success');
        return;
    }

    protected function createRecord() : void
    {
        $url = self::API_URL.'/domains/'.$this->domain.'/records';
        $options = $this->clientOptions;
        $options['json'] = [
            [
                'type' => $this->recordType,
                'name' => $this->recordName,
                'data' => $this->recordValue
            ]
        ];

        $res = $this->client->patch($url, $options);
        $httpCode = $res->getStatusCode();

        if ($httpCode != 200) {
            $this->error('cannot create record, http code : '.$httpCode);
            exit(1);
        }
        $this->info('create record success');
        return;
    }

    protected function getConfig(Config $config): array
    {
        $this->info('retrieve config from env');
        $config = $config->get('godaddy');

        if (empty($config['key'])) {
            $this->error('godaddy key is not set in env, please check .env or config cache');
            exit(1);
        }

        if (empty($config['secret'])) {
            $this->error('godaddy secret is not set in env, please check .env or config cache');
            exit(1);
        }

        if (empty($config['type'])) {
            $config['type'] = self::DEFAULT_RECORD_TYPE;
            $this->info('dns type is not set in env, use '.$config['type'].' as default');
        }

        if (empty($config['name'])) {
            $config['name'] = self::DEFAULT_RECORD_NAME;
            $this->info('dns name is not set in env, use '.$config['name'].' as default');
        }

        if (empty($config['value'])) {
            $config['value'] = $this->getIp();
            $this->info('dns type is not set in env, use public ipv4 : '.$config['value'].' as default');
        }

        return $config;
    }

    protected function getIp() : string
    {
        $res = $this->client->get('ipinfo.io/ip');
        return trim($res->getBody());
    }
}
