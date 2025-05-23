<?php

namespace VendorDuplicator\Aws\S3;

use VendorDuplicator\Aws\Api\ApiProvider;
use VendorDuplicator\Aws\Api\DocModel;
use VendorDuplicator\Aws\Api\Service;
use VendorDuplicator\Aws\AwsClient;
use VendorDuplicator\Aws\CacheInterface;
use VendorDuplicator\Aws\ClientResolver;
use VendorDuplicator\Aws\Command;
use VendorDuplicator\Aws\Exception\AwsException;
use VendorDuplicator\Aws\HandlerList;
use VendorDuplicator\Aws\InputValidationMiddleware;
use VendorDuplicator\Aws\Middleware;
use VendorDuplicator\Aws\Retry\QuotaManager;
use VendorDuplicator\Aws\RetryMiddleware;
use VendorDuplicator\Aws\ResultInterface;
use VendorDuplicator\Aws\CommandInterface;
use VendorDuplicator\Aws\RetryMiddlewareV2;
use VendorDuplicator\Aws\S3\UseArnRegion\Configuration;
use VendorDuplicator\Aws\S3\UseArnRegion\ConfigurationInterface;
use VendorDuplicator\Aws\S3\UseArnRegion\ConfigurationProvider as UseArnRegionConfigurationProvider;
use VendorDuplicator\Aws\S3\RegionalEndpoint\ConfigurationProvider;
use VendorDuplicator\GuzzleHttp\Exception\RequestException;
use VendorDuplicator\GuzzleHttp\Promise\Promise;
use VendorDuplicator\GuzzleHttp\Promise\PromiseInterface;
use VendorDuplicator\Psr\Http\Message\RequestInterface;
/**
 * Client used to interact with **Amazon Simple Storage Service (Amazon S3)**.
 *
 * @method \VendorDuplicator\Aws\Result abortMultipartUpload(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise abortMultipartUploadAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result completeMultipartUpload(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise completeMultipartUploadAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result copyObject(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise copyObjectAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result createBucket(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise createBucketAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result createMultipartUpload(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise createMultipartUploadAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result deleteBucket(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise deleteBucketAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result deleteBucketAnalyticsConfiguration(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise deleteBucketAnalyticsConfigurationAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result deleteBucketCors(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise deleteBucketCorsAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result deleteBucketEncryption(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise deleteBucketEncryptionAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result deleteBucketIntelligentTieringConfiguration(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise deleteBucketIntelligentTieringConfigurationAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result deleteBucketInventoryConfiguration(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise deleteBucketInventoryConfigurationAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result deleteBucketLifecycle(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise deleteBucketLifecycleAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result deleteBucketMetricsConfiguration(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise deleteBucketMetricsConfigurationAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result deleteBucketOwnershipControls(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise deleteBucketOwnershipControlsAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result deleteBucketPolicy(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise deleteBucketPolicyAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result deleteBucketReplication(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise deleteBucketReplicationAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result deleteBucketTagging(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise deleteBucketTaggingAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result deleteBucketWebsite(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise deleteBucketWebsiteAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result deleteObject(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise deleteObjectAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result deleteObjectTagging(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise deleteObjectTaggingAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result deleteObjects(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise deleteObjectsAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result deletePublicAccessBlock(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise deletePublicAccessBlockAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result getBucketAccelerateConfiguration(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise getBucketAccelerateConfigurationAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result getBucketAcl(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise getBucketAclAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result getBucketAnalyticsConfiguration(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise getBucketAnalyticsConfigurationAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result getBucketCors(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise getBucketCorsAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result getBucketEncryption(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise getBucketEncryptionAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result getBucketIntelligentTieringConfiguration(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise getBucketIntelligentTieringConfigurationAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result getBucketInventoryConfiguration(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise getBucketInventoryConfigurationAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result getBucketLifecycle(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise getBucketLifecycleAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result getBucketLifecycleConfiguration(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise getBucketLifecycleConfigurationAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result getBucketLocation(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise getBucketLocationAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result getBucketLogging(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise getBucketLoggingAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result getBucketMetricsConfiguration(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise getBucketMetricsConfigurationAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result getBucketNotification(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise getBucketNotificationAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result getBucketNotificationConfiguration(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise getBucketNotificationConfigurationAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result getBucketOwnershipControls(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise getBucketOwnershipControlsAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result getBucketPolicy(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise getBucketPolicyAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result getBucketPolicyStatus(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise getBucketPolicyStatusAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result getBucketReplication(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise getBucketReplicationAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result getBucketRequestPayment(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise getBucketRequestPaymentAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result getBucketTagging(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise getBucketTaggingAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result getBucketVersioning(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise getBucketVersioningAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result getBucketWebsite(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise getBucketWebsiteAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result getObject(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise getObjectAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result getObjectAcl(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise getObjectAclAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result getObjectAttributes(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise getObjectAttributesAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result getObjectLegalHold(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise getObjectLegalHoldAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result getObjectLockConfiguration(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise getObjectLockConfigurationAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result getObjectRetention(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise getObjectRetentionAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result getObjectTagging(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise getObjectTaggingAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result getObjectTorrent(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise getObjectTorrentAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result getPublicAccessBlock(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise getPublicAccessBlockAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result headBucket(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise headBucketAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result headObject(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise headObjectAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result listBucketAnalyticsConfigurations(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise listBucketAnalyticsConfigurationsAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result listBucketIntelligentTieringConfigurations(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise listBucketIntelligentTieringConfigurationsAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result listBucketInventoryConfigurations(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise listBucketInventoryConfigurationsAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result listBucketMetricsConfigurations(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise listBucketMetricsConfigurationsAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result listBuckets(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise listBucketsAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result listMultipartUploads(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise listMultipartUploadsAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result listObjectVersions(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise listObjectVersionsAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result listObjects(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise listObjectsAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result listObjectsV2(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise listObjectsV2Async(array $args = [])
 * @method \VendorDuplicator\Aws\Result listParts(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise listPartsAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result putBucketAccelerateConfiguration(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise putBucketAccelerateConfigurationAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result putBucketAcl(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise putBucketAclAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result putBucketAnalyticsConfiguration(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise putBucketAnalyticsConfigurationAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result putBucketCors(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise putBucketCorsAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result putBucketEncryption(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise putBucketEncryptionAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result putBucketIntelligentTieringConfiguration(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise putBucketIntelligentTieringConfigurationAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result putBucketInventoryConfiguration(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise putBucketInventoryConfigurationAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result putBucketLifecycle(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise putBucketLifecycleAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result putBucketLifecycleConfiguration(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise putBucketLifecycleConfigurationAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result putBucketLogging(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise putBucketLoggingAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result putBucketMetricsConfiguration(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise putBucketMetricsConfigurationAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result putBucketNotification(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise putBucketNotificationAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result putBucketNotificationConfiguration(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise putBucketNotificationConfigurationAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result putBucketOwnershipControls(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise putBucketOwnershipControlsAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result putBucketPolicy(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise putBucketPolicyAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result putBucketReplication(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise putBucketReplicationAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result putBucketRequestPayment(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise putBucketRequestPaymentAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result putBucketTagging(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise putBucketTaggingAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result putBucketVersioning(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise putBucketVersioningAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result putBucketWebsite(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise putBucketWebsiteAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result putObject(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise putObjectAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result putObjectAcl(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise putObjectAclAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result putObjectLegalHold(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise putObjectLegalHoldAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result putObjectLockConfiguration(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise putObjectLockConfigurationAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result putObjectRetention(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise putObjectRetentionAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result putObjectTagging(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise putObjectTaggingAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result putPublicAccessBlock(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise putPublicAccessBlockAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result restoreObject(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise restoreObjectAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result selectObjectContent(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise selectObjectContentAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result uploadPart(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise uploadPartAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result uploadPartCopy(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise uploadPartCopyAsync(array $args = [])
 * @method \VendorDuplicator\Aws\Result writeGetObjectResponse(array $args = [])
 * @method \VendorDuplicator\GuzzleHttp\Promise\Promise writeGetObjectResponseAsync(array $args = [])
 */
class S3Client extends AwsClient implements S3ClientInterface
{
    use S3ClientTrait;
    /** @var array */
    private static $mandatoryAttributes = ['Bucket', 'Key'];
    public static function getArguments()
    {
        $args = parent::getArguments();
        $args['retries']['fn'] = [__CLASS__, '_applyRetryConfig'];
        $args['api_provider']['fn'] = [__CLASS__, '_applyApiProvider'];
        return $args + ['bucket_endpoint' => ['type' => 'config', 'valid' => ['bool'], 'doc' => 'Set to true to send requests to a hardcoded ' . 'bucket endpoint rather than create an endpoint as a ' . 'result of injecting the bucket into the URL. This ' . 'option is useful for interacting with CNAME endpoints.'], 'use_arn_region' => ['type' => 'config', 'valid' => ['bool', Configuration::class, CacheInterface::class, 'callable'], 'doc' => 'Set to true to allow passed in ARNs to override' . ' client region. Accepts...', 'fn' => [__CLASS__, '_apply_use_arn_region'], 'default' => [UseArnRegionConfigurationProvider::class, 'defaultProvider']], 'use_accelerate_endpoint' => ['type' => 'config', 'valid' => ['bool'], 'doc' => 'Set to true to send requests to an S3 Accelerate' . ' endpoint by default. Can be enabled or disabled on' . ' individual operations by setting' . ' \'@use_accelerate_endpoint\' to true or false. Note:' . ' you must enable S3 Accelerate on a bucket before it can' . ' be accessed via an Accelerate endpoint.', 'default' => \false], 'use_path_style_endpoint' => ['type' => 'config', 'valid' => ['bool'], 'doc' => 'Set to true to send requests to an S3 path style' . ' endpoint by default.' . ' Can be enabled or disabled on individual operations by setting' . ' \'@use_path_style_endpoint\' to true or false.', 'default' => \false], 'disable_multiregion_access_points' => ['type' => 'config', 'valid' => ['bool'], 'doc' => 'Set to true to disable the usage of' . ' multi region access points. These are enabled by default.' . ' Can be enabled or disabled on individual operations by setting' . ' \'@disable_multiregion_access_points\' to true or false.', 'default' => \false]];
    }
    /**
     * {@inheritdoc}
     *
     * In addition to the options available to
     * {@see Aws\AwsClient::__construct}, S3Client accepts the following
     * options:
     *
     * - bucket_endpoint: (bool) Set to true to send requests to a
     *   hardcoded bucket endpoint rather than create an endpoint as a result
     *   of injecting the bucket into the URL. This option is useful for
     *   interacting with CNAME endpoints. Note: if you are using version 2.243.0
     *   and above and do not expect the bucket name to appear in the host, you will
     *   also need to set `use_path_style_endpoint` to `true`.
     * - calculate_md5: (bool) Set to false to disable calculating an MD5
     *   for all Amazon S3 signed uploads.
     * - s3_us_east_1_regional_endpoint:
     *   (Aws\S3\RegionalEndpoint\ConfigurationInterface|Aws\CacheInterface\|callable|string|array)
     *   Specifies whether to use regional or legacy endpoints for the us-east-1
     *   region. Provide an Aws\S3\RegionalEndpoint\ConfigurationInterface object, an
     *   instance of Aws\CacheInterface, a callable configuration provider used
     *   to create endpoint configuration, a string value of `legacy` or
     *   `regional`, or an associative array with the following keys:
     *   endpoint_types: (string)  Set to `legacy` or `regional`, defaults to
     *   `legacy`
     * - use_accelerate_endpoint: (bool) Set to true to send requests to an S3
     *   Accelerate endpoint by default. Can be enabled or disabled on
     *   individual operations by setting '@use_accelerate_endpoint' to true or
     *   false. Note: you must enable S3 Accelerate on a bucket before it can be
     *   accessed via an Accelerate endpoint.
     * - use_arn_region: (Aws\S3\UseArnRegion\ConfigurationInterface,
     *   Aws\CacheInterface, bool, callable) Set to true to enable the client
     *   to use the region from a supplied ARN argument instead of the client's
     *   region. Provide an instance of Aws\S3\UseArnRegion\ConfigurationInterface,
     *   an instance of Aws\CacheInterface, a callable that provides a promise for
     *   a Configuration object, or a boolean value. Defaults to false (i.e.
     *   the SDK will not follow the ARN region if it conflicts with the client
     *   region and instead throw an error).
     * - use_dual_stack_endpoint: (bool) Set to true to send requests to an S3
     *   Dual Stack endpoint by default, which enables IPv6 Protocol.
     *   Can be enabled or disabled on individual operations by setting
     *   '@use_dual_stack_endpoint\' to true or false. Note:
     *   you cannot use it together with an accelerate endpoint.
     * - use_path_style_endpoint: (bool) Set to true to send requests to an S3
     *   path style endpoint by default.
     *   Can be enabled or disabled on individual operations by setting
     *   '@use_path_style_endpoint\' to true or false. Note:
     *   you cannot use it together with an accelerate endpoint.
     * - disable_multiregion_access_points: (bool) Set to true to disable
     *   sending multi region requests.  They are enabled by default.
     *   Can be enabled or disabled on individual operations by setting
     *   '@disable_multiregion_access_points\' to true or false. Note:
     *   you cannot use it together with an accelerate or dualstack endpoint.
     *
     * @param array $args
     */
    public function __construct(array $args)
    {
        if (!isset($args['s3_us_east_1_regional_endpoint']) || $args['s3_us_east_1_regional_endpoint'] instanceof CacheInterface) {
            $args['s3_us_east_1_regional_endpoint'] = ConfigurationProvider::defaultProvider($args);
        }
        $this->addBuiltIns($args);
        parent::__construct($args);
        $stack = $this->getHandlerList();
        $stack->appendInit(SSECMiddleware::wrap($this->getEndpoint()->getScheme()), 's3.ssec');
        $stack->appendBuild(ApplyChecksumMiddleware::wrap($this->getApi()), 's3.checksum');
        $stack->appendBuild(Middleware::contentType(['PutObject', 'UploadPart']), 's3.content_type');
        if ($this->getConfig('bucket_endpoint')) {
            $stack->appendBuild(BucketEndpointMiddleware::wrap(), 's3.bucket_endpoint');
        } elseif (!$this->isUseEndpointV2()) {
            $stack->appendBuild(S3EndpointMiddleware::wrap($this->getRegion(), $this->getConfig('endpoint_provider'), ['accelerate' => $this->getConfig('use_accelerate_endpoint'), 'path_style' => $this->getConfig('use_path_style_endpoint'), 'use_fips_endpoint' => $this->getConfig('use_fips_endpoint'), 'dual_stack' => $this->getConfig('use_dual_stack_endpoint')->isUseDualStackEndpoint()]), 's3.endpoint_middleware');
        }
        $stack->appendBuild(BucketEndpointArnMiddleware::wrap($this->getApi(), $this->getRegion(), ['use_arn_region' => $this->getConfig('use_arn_region'), 'accelerate' => $this->getConfig('use_accelerate_endpoint'), 'path_style' => $this->getConfig('use_path_style_endpoint'), 'dual_stack' => $this->getConfig('use_dual_stack_endpoint')->isUseDualStackEndpoint(), 'use_fips_endpoint' => $this->getConfig('use_fips_endpoint'), 'disable_multiregion_access_points' => $this->getConfig('disable_multiregion_access_points'), 'endpoint' => isset($args['endpoint']) ? $args['endpoint'] : null], $this->isUseEndpointV2()), 's3.bucket_endpoint_arn');
        $stack->appendValidate(InputValidationMiddleware::wrap($this->getApi(), self::$mandatoryAttributes), 'input_validation_middleware');
        $stack->appendSign(PutObjectUrlMiddleware::wrap(), 's3.put_object_url');
        $stack->appendSign(PermanentRedirectMiddleware::wrap(), 's3.permanent_redirect');
        $stack->appendInit(Middleware::sourceFile($this->getApi()), 's3.source_file');
        $stack->appendInit($this->getSaveAsParameter(), 's3.save_as');
        $stack->appendInit($this->getLocationConstraintMiddleware(), 's3.location');
        $stack->appendInit($this->getEncodingTypeMiddleware(), 's3.auto_encode');
        $stack->appendInit($this->getHeadObjectMiddleware(), 's3.head_object');
        if ($this->isUseEndpointV2()) {
            $this->processEndpointV2Model();
            $stack->after('builderV2', 's3.check_empty_path_with_query', $this->getEmptyPathWithQuery());
        }
    }
    /**
     * Determine if a string is a valid name for a DNS compatible Amazon S3
     * bucket.
     *
     * DNS compatible bucket names can be used as a subdomain in a URL (e.g.,
     * "<bucket>.s3.amazonaws.com").
     *
     * @param string $bucket Bucket name to check.
     *
     * @return bool
     */
    public static function isBucketDnsCompatible($bucket)
    {
        if (!is_string($bucket)) {
            return \false;
        }
        $bucketLen = strlen($bucket);
        return $bucketLen >= 3 && $bucketLen <= 63 && !filter_var($bucket, \FILTER_VALIDATE_IP) && preg_match('/^[a-z0-9]([a-z0-9\-\.]*[a-z0-9])?$/', $bucket);
    }
    public static function _apply_use_arn_region($value, array &$args, HandlerList $list)
    {
        if ($value instanceof CacheInterface) {
            $value = UseArnRegionConfigurationProvider::defaultProvider($args);
        }
        if (is_callable($value)) {
            $value = $value();
        }
        if ($value instanceof PromiseInterface) {
            $value = $value->wait();
        }
        if ($value instanceof ConfigurationInterface) {
            $args['use_arn_region'] = $value;
        } else {
            // The Configuration class itself will validate other inputs
            $args['use_arn_region'] = new Configuration($value);
        }
    }
    public function createPresignedRequest(CommandInterface $command, $expires, array $options = [])
    {
        $command = clone $command;
        $command->getHandlerList()->remove('signer');
        $request = \VendorDuplicator\Aws\serialize($command);
        $signing_name = empty($command->getAuthSchemes()) ? $this->getSigningName($request->getUri()->getHost()) : $command->getAuthSchemes()['name'];
        $signature_version = empty($command->getAuthSchemes()) ? $this->getConfig('signature_version') : $command->getAuthSchemes()['version'];
        /** @var \VendorDuplicator\Aws\Signature\SignatureInterface $signer */
        $signer = call_user_func($this->getSignatureProvider(), $signature_version, $signing_name, $this->getConfig('signing_region'));
        return $signer->presign($request, $this->getCredentials()->wait(), $expires, $options);
    }
    /**
     * Returns the URL to an object identified by its bucket and key.
     *
     * The URL returned by this method is not signed nor does it ensure that the
     * bucket and key given to the method exist. If you need a signed URL, then
     * use the {@see \Aws\S3\S3Client::createPresignedRequest} method and get
     * the URI of the signed request.
     *
     * @param string $bucket  The name of the bucket where the object is located
     * @param string $key     The key of the object
     *
     * @return string The URL to the object
     */
    public function getObjectUrl($bucket, $key)
    {
        $command = $this->getCommand('GetObject', ['Bucket' => $bucket, 'Key' => $key]);
        return (string) \VendorDuplicator\Aws\serialize($command)->getUri();
    }
    /**
     * Raw URL encode a key and allow for '/' characters
     *
     * @param string $key Key to encode
     *
     * @return string Returns the encoded key
     */
    public static function encodeKey($key)
    {
        return str_replace('%2F', '/', rawurlencode($key));
    }
    /**
     * Provides a middleware that removes the need to specify LocationConstraint on CreateBucket.
     *
     * @return \Closure
     */
    private function getLocationConstraintMiddleware()
    {
        $region = $this->getRegion();
        return static function (callable $handler) use ($region) {
            return function (Command $command, $request = null) use ($handler, $region) {
                if ($command->getName() === 'CreateBucket') {
                    $locationConstraint = isset($command['CreateBucketConfiguration']['LocationConstraint']) ? $command['CreateBucketConfiguration']['LocationConstraint'] : null;
                    if ($locationConstraint === 'us-east-1') {
                        unset($command['CreateBucketConfiguration']);
                    } elseif ('us-east-1' !== $region && empty($locationConstraint)) {
                        $command['CreateBucketConfiguration'] = ['LocationConstraint' => $region];
                    }
                }
                return $handler($command, $request);
            };
        };
    }
    /**
     * Provides a middleware that supports the `SaveAs` parameter.
     *
     * @return \Closure
     */
    private function getSaveAsParameter()
    {
        return static function (callable $handler) {
            return function (Command $command, $request = null) use ($handler) {
                if ($command->getName() === 'GetObject' && isset($command['SaveAs'])) {
                    $command['@http']['sink'] = $command['SaveAs'];
                    unset($command['SaveAs']);
                }
                return $handler($command, $request);
            };
        };
    }
    /**
     * Provides a middleware that disables content decoding on HeadObject
     * commands.
     *
     * @return \Closure
     */
    private function getHeadObjectMiddleware()
    {
        return static function (callable $handler) {
            return function (CommandInterface $command, RequestInterface $request = null) use ($handler) {
                if ($command->getName() === 'HeadObject' && !isset($command['@http']['decode_content'])) {
                    $command['@http']['decode_content'] = \false;
                }
                return $handler($command, $request);
            };
        };
    }
    /**
     * Provides a middleware that autopopulates the EncodingType parameter on
     * ListObjects commands.
     *
     * @return \Closure
     */
    private function getEncodingTypeMiddleware()
    {
        return static function (callable $handler) {
            return function (Command $command, $request = null) use ($handler) {
                $autoSet = \false;
                if ($command->getName() === 'ListObjects' && empty($command['EncodingType'])) {
                    $command['EncodingType'] = 'url';
                    $autoSet = \true;
                }
                return $handler($command, $request)->then(function (ResultInterface $result) use ($autoSet) {
                    if ($result['EncodingType'] === 'url' && $autoSet) {
                        static $topLevel = ['Delimiter', 'Marker', 'NextMarker', 'Prefix'];
                        static $nested = [['Contents', 'Key'], ['CommonPrefixes', 'Prefix']];
                        foreach ($topLevel as $key) {
                            if (isset($result[$key])) {
                                $result[$key] = urldecode($result[$key]);
                            }
                        }
                        foreach ($nested as $steps) {
                            if (isset($result[$steps[0]])) {
                                foreach ($result[$steps[0]] as $key => $part) {
                                    if (isset($part[$steps[1]])) {
                                        $result[$steps[0]][$key][$steps[1]] = urldecode($part[$steps[1]]);
                                    }
                                }
                            }
                        }
                    }
                    return $result;
                });
            };
        };
    }
    /**
     * Provides a middleware that checks for an empty path and a
     * non-empty query string.
     *
     * @return \Closure
     */
    private function getEmptyPathWithQuery()
    {
        return static function (callable $handler) {
            return function (Command $command, RequestInterface $request) use ($handler) {
                $uri = $request->getUri();
                if (empty($uri->getPath()) && !empty($uri->getQuery())) {
                    $uri = $uri->withPath('/');
                    $request = $request->withUri($uri);
                }
                return $handler($command, $request);
            };
        };
    }
    /**
     * Special handling for when the service name is s3-object-lambda.
     * So, if the host contains s3-object-lambda, then the service name
     * returned is s3-object-lambda, otherwise the default signing service is returned.
     * @param string $host The host to validate if is a s3-object-lambda URL.
     * @return string returns the signing service name to be used
     */
    private function getSigningName($host)
    {
        if (strpos($host, 's3-object-lambda')) {
            return 's3-object-lambda';
        }
        return $this->getConfig('signing_name');
    }
    /**
     * Modifies API definition to remove `Bucket` from request URIs.
     * This is now handled by the endpoint ruleset.
     *
     * @return void
     *
     * @internal
     */
    private function processEndpointV2Model()
    {
        $definition = $this->getApi()->getDefinition();
        foreach ($definition['operations'] as &$operation) {
            if (isset($operation['http']['requestUri'])) {
                $requestUri = $operation['http']['requestUri'];
                if ($requestUri === "/{Bucket}") {
                    $requestUri = str_replace('/{Bucket}', '/', $requestUri);
                } else {
                    $requestUri = str_replace('/{Bucket}', '', $requestUri);
                }
                $operation['http']['requestUri'] = $requestUri;
            }
        }
        $this->getApi()->setDefinition($definition);
    }
    /**
     * Adds service-specific client built-in values
     *
     * @return void
     */
    private function addBuiltIns($args)
    {
        if ($args['region'] !== 'us-east-1') {
            return \false;
        }
        $key = 'AWS::S3::UseGlobalEndpoint';
        $result = $args['s3_us_east_1_regional_endpoint'] instanceof \Closure ? $args['s3_us_east_1_regional_endpoint']()->wait() : $args['s3_us_east_1_regional_endpoint'];
        if (is_string($result)) {
            if ($result === 'regional') {
                $value = \false;
            } else if ($result === 'legacy') {
                $value = \true;
            } else {
                return;
            }
        } else if ($result->isFallback() || $result->getEndpointsType() === 'legacy') {
            $value = \true;
        } else {
            $value = \false;
        }
        $this->clientBuiltIns[$key] = $value;
    }
    /** @internal */
    public static function _applyRetryConfig($value, $args, HandlerList $list)
    {
        if ($value) {
            $config = \VendorDuplicator\Aws\Retry\ConfigurationProvider::unwrap($value);
            if ($config->getMode() === 'legacy') {
                $maxRetries = $config->getMaxAttempts() - 1;
                $decider = RetryMiddleware::createDefaultDecider($maxRetries);
                $decider = function ($retries, $command, $request, $result, $error) use ($decider, $maxRetries) {
                    $maxRetries = null !== $command['@retries'] ? $command['@retries'] : $maxRetries;
                    if ($decider($retries, $command, $request, $result, $error)) {
                        return \true;
                    }
                    if ($error instanceof AwsException && $retries < $maxRetries) {
                        if ($error->getResponse() && $error->getResponse()->getStatusCode() >= 400) {
                            return strpos($error->getResponse()->getBody(), 'Your socket connection to the server') !== \false;
                        }
                        if ($error->getPrevious() instanceof RequestException) {
                            // All commands except CompleteMultipartUpload are
                            // idempotent and may be retried without worry if a
                            // networking error has occurred.
                            return $command->getName() !== 'CompleteMultipartUpload';
                        }
                    }
                    return \false;
                };
                $delay = [RetryMiddleware::class, 'exponentialDelay'];
                $list->appendSign(Middleware::retry($decider, $delay), 'retry');
            } else {
                $defaultDecider = RetryMiddlewareV2::createDefaultDecider(new QuotaManager(), $config->getMaxAttempts());
                $list->appendSign(RetryMiddlewareV2::wrap($config, ['collect_stats' => $args['stats']['retries'], 'decider' => function ($attempts, CommandInterface $cmd, $result) use ($defaultDecider, $config) {
                    $isRetryable = $defaultDecider($attempts, $cmd, $result);
                    if (!$isRetryable && $result instanceof AwsException && $attempts < $config->getMaxAttempts()) {
                        if (!empty($result->getResponse()) && $result->getResponse()->getStatusCode() >= 400) {
                            return strpos($result->getResponse()->getBody(), 'Your socket connection to the server') !== \false;
                        }
                        if ($result->getPrevious() instanceof RequestException && $cmd->getName() !== 'CompleteMultipartUpload') {
                            $isRetryable = \true;
                        }
                    }
                    return $isRetryable;
                }]), 'retry');
            }
        }
    }
    /** @internal */
    public static function _applyApiProvider($value, array &$args, HandlerList $list)
    {
        ClientResolver::_apply_api_provider($value, $args);
        $args['parser'] = new GetBucketLocationParser(new ValidateResponseChecksumParser(new AmbiguousSuccessParser(new RetryableMalformedResponseParser($args['parser'], $args['exception_class']), $args['error_parser'], $args['exception_class']), $args['api']));
    }
    /**
     * @internal
     * @codeCoverageIgnore
     */
    public static function applyDocFilters(array $api, array $docs)
    {
        $b64 = '<div class="alert alert-info">This value will be base64 encoded on your behalf.</div>';
        $opt = '<div class="alert alert-info">This value will be computed for you it is not supplied.</div>';
        // Add a note on the CopyObject docs
        $s3ExceptionRetryMessage = "<p>Additional info on response behavior: if there is" . " an internal error in S3 after the request was successfully recieved," . " a 200 response will be returned with an <code>S3Exception</code> embedded" . " in it; this will still be caught and retried by" . " <code>RetryMiddleware.</code></p>";
        $docs['operations']['CopyObject'] .= $s3ExceptionRetryMessage;
        $docs['operations']['CompleteMultipartUpload'] .= $s3ExceptionRetryMessage;
        $docs['operations']['UploadPartCopy'] .= $s3ExceptionRetryMessage;
        $docs['operations']['UploadPart'] .= $s3ExceptionRetryMessage;
        // Add note about stream ownership in the putObject call
        $guzzleStreamMessage = "<p>Additional info on behavior of the stream" . " parameters: Psr7 takes ownership of streams and will automatically close" . " streams when this method is called with a stream as the <code>Body</code>" . " parameter.  To prevent this, set the <code>Body</code> using" . " <code>GuzzleHttp\\Psr7\\stream_for</code> method with a is an instance of" . " <code>Psr\\Http\\Message\\StreamInterface</code>, and it will be returned" . " unmodified. This will allow you to keep the stream in scope. </p>";
        $docs['operations']['PutObject'] .= $guzzleStreamMessage;
        // Add the SourceFile parameter.
        $docs['shapes']['SourceFile']['base'] = 'The path to a file on disk to use instead of the Body parameter.';
        $api['shapes']['SourceFile'] = ['type' => 'string'];
        $api['shapes']['PutObjectRequest']['members']['SourceFile'] = ['shape' => 'SourceFile'];
        $api['shapes']['UploadPartRequest']['members']['SourceFile'] = ['shape' => 'SourceFile'];
        // Add the ContentSHA256 parameter.
        $docs['shapes']['ContentSHA256']['base'] = 'A SHA256 hash of the body content of the request.';
        $api['shapes']['ContentSHA256'] = ['type' => 'string'];
        $api['shapes']['PutObjectRequest']['members']['ContentSHA256'] = ['shape' => 'ContentSHA256'];
        $api['shapes']['UploadPartRequest']['members']['ContentSHA256'] = ['shape' => 'ContentSHA256'];
        $docs['shapes']['ContentSHA256']['append'] = $opt;
        // Add the AddContentMD5 parameter.
        $docs['shapes']['AddContentMD5']['base'] = 'Set to true to calculate the ContentMD5 for the upload.';
        $api['shapes']['AddContentMD5'] = ['type' => 'boolean'];
        $api['shapes']['PutObjectRequest']['members']['AddContentMD5'] = ['shape' => 'AddContentMD5'];
        $api['shapes']['UploadPartRequest']['members']['AddContentMD5'] = ['shape' => 'AddContentMD5'];
        // Add the SaveAs parameter.
        $docs['shapes']['SaveAs']['base'] = 'The path to a file on disk to save the object data.';
        $api['shapes']['SaveAs'] = ['type' => 'string'];
        $api['shapes']['GetObjectRequest']['members']['SaveAs'] = ['shape' => 'SaveAs'];
        // Several SSECustomerKey documentation updates.
        $docs['shapes']['SSECustomerKey']['append'] = $b64;
        $docs['shapes']['CopySourceSSECustomerKey']['append'] = $b64;
        $docs['shapes']['SSECustomerKeyMd5']['append'] = $opt;
        // Add the ObjectURL to various output shapes and documentation.
        $docs['shapes']['ObjectURL']['base'] = 'The URI of the created object.';
        $api['shapes']['ObjectURL'] = ['type' => 'string'];
        $api['shapes']['PutObjectOutput']['members']['ObjectURL'] = ['shape' => 'ObjectURL'];
        $api['shapes']['CopyObjectOutput']['members']['ObjectURL'] = ['shape' => 'ObjectURL'];
        $api['shapes']['CompleteMultipartUploadOutput']['members']['ObjectURL'] = ['shape' => 'ObjectURL'];
        // Fix references to Location Constraint.
        unset($api['shapes']['CreateBucketRequest']['payload']);
        $api['shapes']['BucketLocationConstraint']['enum'] = ["ap-northeast-1", "ap-southeast-2", "ap-southeast-1", "cn-north-1", "eu-central-1", "eu-west-1", "us-east-1", "us-west-1", "us-west-2", "sa-east-1"];
        // Add a note that the ContentMD5 is automatically computed, except for with PutObject and UploadPart
        $docs['shapes']['ContentMD5']['append'] = '<div class="alert alert-info">The value will be computed on ' . 'your behalf.</div>';
        $docs['shapes']['ContentMD5']['excludeAppend'] = ['PutObjectRequest', 'UploadPartRequest'];
        //Add a note to ContentMD5 for PutObject and UploadPart that specifies the value is required
        // When uploading to a bucket with object lock enabled and that it is not computed automatically
        $objectLock = '<div class="alert alert-info">This value is required if uploading to a bucket ' . 'which has Object Lock enabled. It will not be calculated for you automatically. If you wish to have ' . 'the value calculated for you, use the `AddContentMD5` parameter.</div>';
        $docs['shapes']['ContentMD5']['appendOnly'] = ['message' => $objectLock, 'shapes' => ['PutObjectRequest', 'UploadPartRequest']];
        return [new Service($api, ApiProvider::defaultProvider()), new DocModel($docs)];
    }
    /**
     * @internal
     * @codeCoverageIgnore
     */
    public static function addDocExamples($examples)
    {
        $getObjectExample = ['input' => ['Bucket' => 'arn:aws:s3:us-east-1:123456789012:accesspoint:myaccesspoint', 'Key' => 'my-key'], 'output' => ['Body' => 'class GuzzleHttp\Psr7\Stream#208 (7) {...}', 'ContentLength' => '11', 'ContentType' => 'application/octet-stream'], 'comments' => ['input' => '', 'output' => 'Simplified example output'], 'description' => 'The following example retrieves an object by referencing the bucket via an S3 accesss point ARN. Result output is simplified for the example.', 'id' => '', 'title' => 'To get an object via an S3 access point ARN'];
        if (isset($examples['GetObject'])) {
            $examples['GetObject'][] = $getObjectExample;
        } else {
            $examples['GetObject'] = [$getObjectExample];
        }
        $putObjectExample = ['input' => ['Bucket' => 'arn:aws:s3:us-east-1:123456789012:accesspoint:myaccesspoint', 'Key' => 'my-key', 'Body' => 'my-body'], 'output' => ['ObjectURL' => 'https://my-bucket.s3.us-east-1.amazonaws.com/my-key'], 'comments' => ['input' => '', 'output' => 'Simplified example output'], 'description' => 'The following example uploads an object by referencing the bucket via an S3 accesss point ARN. Result output is simplified for the example.', 'id' => '', 'title' => 'To upload an object via an S3 access point ARN'];
        if (isset($examples['PutObject'])) {
            $examples['PutObject'][] = $putObjectExample;
        } else {
            $examples['PutObject'] = [$putObjectExample];
        }
        return $examples;
    }
}
