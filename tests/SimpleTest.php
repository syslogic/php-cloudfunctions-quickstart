<?php
namespace CloudFunctions\Test;

use CloudEvents\V1\CloudEventImmutable;
use GuzzleHttp\Psr7\ServerRequest;
use CloudFunctions\CloudFunctions;
use PHPUnit\Framework\TestCase;

/**
 * Simple {@link TestCase}
 * @author Martin Zeitler
 * @version 1.0.0
 */
class SimpleTest extends TestCase {

    private static string $project_id;
    private static array $json_data;
    private static string $pubsub_topic;
    private static string $gcs_bucket;

    public static function setUpBeforeClass(): void {
        require_once __DIR__.'/../index.php';
        self::$project_id   = self::get_project_id();
        self::$json_data    = ['data' => uniqid()];
        self::$pubsub_topic = 'play-notifications';
        self::$gcs_bucket   = 'gs://php-cloudfunctions';
    }

    /** It depends on the <code>gcloud</code> command to obtain the project ID. */
    private static function get_project_id(): string {
        $stdout = shell_exec('gcloud config get-value project');
        if ($stdout == null) {return 'unknown';}
        else {return $stdout;}
    }

    /** Test: On HTTPS */
    public function test_on_https(): void {
        $payload = json_encode( self::$json_data );
        $request = new ServerRequest('POST', '/', [], $payload);
        $response = CloudFunctions::on_https( $request );
        // $this->assertTrue($response->getReasonPhrase().equals("OK"));
        $this->assertTrue($response->getStatusCode() == 200);
    }

    /** Test: On Pub/Sub Event */
    public function test_on_pubsub(): void {
        CloudFunctions::on_pubsub(new CloudEventImmutable(
            uniqId(), // id
            '//pubsub.googleapis.com/projects/'.self::$project_id.'/topics/'.self::$pubsub_topic, // source
            'google.cloud.pubsub.topic.v1.messagePublished', // type
            ['data' => base64_encode('Test')], // data
            'application/json' // data content-type
        ));
        self::assertTrue( true );
    }

    /** Test: On GCS Event */
    public function test_on_gcs(): void {
        CloudFunctions::on_gcs(new CloudEventImmutable(
            uniqId(), // id
            'pubsub.googleapis.com', // source
            'google.storage.object.finalize', [ // type
                'bucket' => self::$gcs_bucket,
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
