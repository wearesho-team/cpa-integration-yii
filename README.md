# CPA Integration for Yii2

[![codecov](https://codecov.io/gh/wearesho-team/cpa-integration-yii/branch/master/graph/badge.svg)](https://codecov.io/gh/wearesho-team/cpa-integration-yii)
[![Build Status](https://travis-ci.org/wearesho-team/cpa-integration-yii.svg?branch=master)](https://travis-ci.org/wearesho-team/cpa-integration-yii)
[![License](https://poser.pugx.org/wearesho-team/cpa-integration-yii/license)](https://packagist.org/packages/wearesho-team/cpa-integration-yii)
[![Latest Stable Version](https://poser.pugx.org/wearesho-team/cpa-integration-yii/version)](https://packagist.org/packages/wearesho-team/cpa-integration-yii)


See [original repository](https://github.com/wearesho-team/cpa-integration) for details.

## Contents
### Repositories
#### Session repositories
1. [LeadSessionRepository](./docs/LeadSessionRepository.md) 
Session storage for CPA leads (use default Yii2 session manager, \Yii::$app->session)
#### Database repositories
*Notice: you can use your own ActiveRecord implementation of storage, see repository documentation for details* 
1. [LeadRepository](./docs/LeadRepository.md) - 
Database repository for CPA leads 
(includes [ActiveRecord model](./src/Models/StoredLead.php) and [migration](./migrations/m170910_122042_create_stored_lead_table.php))

3. [ConversionRepository](./docs/ConversionRepository.md) - 
Database repository for CPA conversions 
(includes [ActiveRecord model](./src/Models/StoredConversionRecord.php) and [migration](./migrations/m170910_122053_create_stored_conversion_table.php)).

### Behaviors
1. [LeadBehavior](./docs/LeadBehavior) - 
Allow you to catch lead and store it in LeadRepository
2. [ConversionBehavior](./docs/ConversionBehavior) - 
Allow you to convert lead from LeadRepository to conversion 
(using ConversionRepository to save it)

### Migrations
In your project directory, after library installation
```bash
php yii migrate --migrationPath=vendor/wearesho-team/cpa-integration-yii/migrations
```

### Installation
```bash
composer require wearesho-team/cpa-integration-yii
```


## Contributors
1. [Alexander <horat1us> Letnikow](https://github.com/Horat1us)

## License
[MIT](./LICENSE)
