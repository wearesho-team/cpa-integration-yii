# LeadBehavior
Handling and storing lead before controller action.  
Designed for `\yii\web\Controller`

## Example
```php
<?php

use yii\web\Controller;

use Wearesho\Cpa\Yii\Behaviors\LeadBehavior;

use Wearesho\Cpa\Yii\Repositories\LeadRepository;
use Wearesho\Cpa\Yii\Repositories\LeadSessionRepository;

class LandingController extends Controller
{
    public function behaviors()
    {
        $leadRepository = \Yii::$app->user->getIdentity()
            ? new LeadRepository() // Using database storage if user logged in
            : new LeadSessionRepository(); // Using session while use not logged in
        
        return [
            'handleLead' => [
                'class' => LeadBehavior::class,
                'leadRepository' => $leadRepository,
            ],
        ];
    }
}
```
