<?php
namespace Quickstart\Test;

use CloudEvents\V1\CloudEventImmutable;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Quickstart\CloudFunctions;
use Google\CloudFunctions\CloudEvent;
use PHPUnit\Framework\TestCase;

/**
 * Class SimpleTestCase.
 *
 * @author Martin Zeitler
 * @version 1.0.0
 */
class SimpleTestCase extends TestCase {

    private static string $project_id;
    private static string $test_topic;
    private static string $test_bucket;

    public static function setUpBeforeClass(): void {
        require_once __DIR__.'/../index.php';
        self::$project_id = self::get_project_id();
        self::$test_topic = 'play-notifications';
        self::$test_bucket = 'gs://php-cloudfunctions';
    }

    private static function get_project_id() {
        return shell_exec('gcloud config get-value project');
    }

    public function test_on_https(): void {
        $payload = json_encode(['data' => uniqid()]);
        $request = new ServerRequest('POST', '/', [], $payload);
        $response = CloudFunctions::on_https( $request );
        $this->assertTrue($response instanceof Response);
    }

    public function test_on_pubsub(): void {
        CloudFunctions::on_pubsub(new CloudEventImmutable(
            uniqId(), // id
            '//pubsub.googleapis.com/projects/'.self::$project_id.'/topics/'.self::$test_topic, // source
            'google.cloud.pubsub.topic.v1.messagePublished', // type
            ['data' => base64_encode('Test')], // data
            'application/json' // data content-type
        ));
        self::assertTrue( true );
    }

    public function test_on_gcs(): void {
        CloudFunctions::on_gcs(new CloudEventImmutable(
            uniqId(), // id
            'pubsub.googleapis.com', // source
            'google.storage.object.finalize', [ // type
                'bucket' => self::$test_bucket,
                'name' => 'test.json',
                'metageneration' => '',
                'timeCreated' => time(),
                'updated' => time()
            ], // data
            'application/json' // data content-type
        ));
        self::assertTrue( true );
    }
}
