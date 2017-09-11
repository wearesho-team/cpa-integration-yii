# ConversionBehavior
Sending conversion on some action

## Example
```php
<?php

use yii\base\Event;
use yii\web\Controller;

use Wearesho\Cpa\Yii\Behaviors\ConversionBehavior;
use Wearesho\Cpa\Yii\Interfaces\ConversionSourceInterface;

use Wearesho\Cpa\Yii\Repositories\LeadRepository;
use Wearesho\Cpa\Yii\Repositories\LeadSessionRepository;

/**
 * Class LandingController
 */
class LandingController extends Controller
{
    public function behaviors()
    {
        // Use DependencyInjection container instead of this condition
        $leadRepository = \Yii::$app->user->getIdentity()
            ? new LeadRepository() // Using database storage if user logged in
            : new LeadSessionRepository(); // Using session while use not logged in

        return [
            'conversion' => [
                'class' => ConversionBehavior::class,
                'leadRepository' => $leadRepository,
                'postbackConfig' => function () {
                    // Bad practice, for example only
                    return yaml_parse(\Yii::getAlias('@common/config/cpa.yml'));
                },
            ],
        ];
    }

    public function actionRegister($email)
    {
        $user = UsersRepository::create($email);
        // User or anything else must implement interface
        if (!$user instanceof ConversionSourceInterface) {
            throw new UnexpectedValueException();
        }
        try {
            $this->trigger(ConversionBehavior::EVENT_CONVERSION_REGISTERED, new Event([
                'sender' => $user,
            ]));
        }
        catch(\Wearesho\Cpa\Exceptions\DuplicatedConversionException $exception) {
            // log if you already send conversion with specified id
        }
        catch(\GuzzleHttp\Exception\RequestException $exception) {
            // log if cpa network give us error
        }
        catch(\Wearesho\Cpa\Exceptions\UnsupportedConversionTypeException $exception) {
            // log if you did not configure some cpa network for current lead
        }
    }
}
```