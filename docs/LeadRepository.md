# LeadRepository
Storing lead across requests.  
*Case: you have API, API takes Lead and saves it related to current user*

## Example
### Creating default repository with default Yii user
```php
<?php
use Wearesho\Cpa\Yii\Repositories\LeadRepository;
use Wearesho\Cpa\SalesDoubler\Lead as SalesDoublerLead;

$repository = new LeadRepository();
$repository->push(new SalesDoublerLead($_POST['click_id']));
```
*Notice: it's bad practice not to pass identity to constructor because it can break your console methods* 
### Catching leads for few users
```php
<?php
use Wearesho\Cpa\Yii\Repositories\LeadRepository;
use Wearesho\Cpa\SalesDoubler\Lead as SalesDoublerLead;


$users = UsersRepository::getActive();
foreach($users as $user) {
    if(!array_key_exists($user->id, $_POST)) {
        continue;
    }
    
    $repository = new LeadRepository($user);
    $repository->push(new SalesDoublerLead($_POST[$user->id]["click_id"]));
}
``` 
### Using custom ActiveRecord model
```php
<?php
use Wearesho\Cpa\Yii\Repositories\LeadRepository;
use \Wearesho\Cpa\Yii\Models\StoredLeadInterface;

$model = new CustomStoredLeadActiveRecord();
if(!$model instanceof StoredLeadInterface) {
    throw new UnexpectedValueException(
        "Custom model must implement interface"
    );
}
// Just pass model instance to constructor
$repository = new LeadRepository(null, $model); 
```