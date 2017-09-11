# LeadSessionRepository
Storing leads across session.
  
*Case: user with lead did not login, so you can use database repository until user login*  
*Notice: it uses global object `\Yii::$app->session`*

## Example
```php
<?php

use Wearesho\Cpa\Yii\Repositories\LeadSessionRepository;

use Wearesho\Cpa\Lead\LeadFactory;
use Wearesho\Cpa\Interfaces\LeadInterface;

$factory = new LeadFactory();
$lead = $factory->fromUrl(\Yii::$app->request->url);
if ($lead instanceof LeadInterface) {
    $repository = new LeadSessionRepository($yourSessionKey = "storedLead");
    $repository->push($lead);
}
```