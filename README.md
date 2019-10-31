# Cloud CMS PHP Driver

The [Cloud CMS](https://www.cloudcms.com/) PHP driver is a client library used to facilitate connections to the Cloud CMS API. The driver handles OAuth authentication and token management, HTTPS calls, and provides convenient methods and classes with which to perform content operations. It works against Cloud CMS instances on our SaaS platform as well as on-premise installations.

## Installation

To install, run:

```
composer require cloudcms/cloudcms
```

## Examples

Below are some examples of how you might use this driver:

```php

use CloudCMS\CloudCMS;

// Load configuration from gitana.json file
$config_string = file_get_contents("gitana.json");
$config = json_decode($config_string, true);

// Connect to CloudCMS
$client = new CloudCMS();
$platform = $client->connect($config);

// List Repositories
$repositories = $platform->listRepositories();

// Read Repository
$repository = $platform->readRepository("<repositoryId>");

// List Branches
$branches = $repository->listBranches();

// Read Branch
$branch = $repository->readBranch("<branchId>");

// Read Node
$node = $branch->readNode("<nodeId>");

// Create Node
$obj = array(
    "title" => "Twelfth Night",
    "description" => "An old play"
);
$newNode = $branch->createNode($obj);

// Query Nodes
$query = array(
    "_type" => "store:book"
);
$pagination = array(
    "limit" => 2
);
$queriedNodes = $branch->queryNodes($query, $pagination);

// Find Nodes
$find = array(
    "search" => "Shakespeare",
    "query" => array(
        "_type" => "store:book"
    )
);
$foundNodes = $branch->findNodes($find);
```

## Tests

To run the tests for this driver, ensure that you have a `gitana.json` file in the driver directory, then run:

```
composer test
```

## Resources

- Cloud CMS: https://www.cloudcms.com
- Github: http://github.com/gitana/cloudcms-php-driver
- PHP Driver Download: https://packagist.org/packages/cloudcms/cloudcms
- Cloud CMS Documentation: https://www.cloudcms.com/documentation.html
- Developers Guide: https://www.cloudcms.com/developers.html

## Support

For information or questions about the PHP Driver, please contact Cloud CMS
at [support@cloudcms.com](mailto:support@cloudcms.com).
