<?php
namespace CloudFunctions;

use CloudEvents\V1\CloudEventInterface;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;

/**
 * Class CloudFunctions.
 *
 * @author Martin Zeitler
 * @version 1.0.0
 */
class CloudFunctions {

    private static stdClass $response;

    /**  */
    public static function on_https( ServerRequestInterface $request ): ResponseInterface {
        $params = $request->getQueryParams();
        $body = sprintf('Hello, %s!', $params['name'] ?? 'World');
        return (new Response())
            ->withBody(Utils::streamFor($body))
            ->withStatus(200);
    }

    /**  */
    public static function on_pubsub( CloudEventInterface $event ): void {
        $log = fopen(getenv('LOGGER_OUTPUT') ?: 'php://stdout', 'wb');
        $cloudEventData = $event->getData();
        $pubSubData = base64_decode($cloudEventData['data']);
        $name = $pubSubData ? htmlspecialchars($pubSubData) : 'World';
        fwrite($log, "Hello, $name!" . PHP_EOL);
    }

    /**  */
    public static function on_gcs( CloudEventInterface $event ): void {
        $log = fopen(getenv('LOGGER_OUTPUT') ?: 'php://stdout', 'wb');
        $data = $event->getData();
        fwrite($log, 'Event: ' . $event->getId() . PHP_EOL);
        fwrite($log, 'Event Type: ' . $event->getType() . PHP_EOL);
        fwrite($log, 'Bucket: ' . $data['bucket'] . PHP_EOL);
        fwrite($log, 'File: ' . $data['name'] . PHP_EOL);
        fwrite($log, 'Metageneration: ' . $data['metageneration'] . PHP_EOL);
        fwrite($log, 'Created: ' . $data['timeCreated'] . PHP_EOL);
        fwrite($log, 'Updated: ' . $data['updated'] . PHP_EOL);
    }
}
