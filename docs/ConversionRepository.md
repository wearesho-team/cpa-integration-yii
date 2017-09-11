# ConversionRepository
Storing information about sent conversions using ActiveRecord
*Case: dependency injection for `\Wearesho\Cpa\Postback\PostbackService`*

## Example
```php
<?php
use Wearesho\Cpa\Postback\PostbackService;
use Wearesho\Cpa\Yii\Repositories\LeadSessionRepository;
use Wearesho\Cpa\Yii\Repositories\ConversionRepository;

$lead = (new LeadSessionRepository)->pull();
if ($lead instanceof $lead) {
   $conversionId = get_conversion_id();
   $conversion = $lead->createConversion($conversionId);
   
   $sender = new PostbackService(
       new ConversionRepository(),
       new \GuzzleHttp\Client()
   );
   // ConversionRepository::push will be triggered
   $sender->send($conversion); 
}
```
