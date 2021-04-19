```php
FileJsonParser::parse(string $directory, [requirements])
  ->onMainRequirementNotMet(string $identifier)
  ->addParseComponent(ComponentParser::getForParsing("nameIdentifier", $name, $identifier), )
  ->addParseComponent(ComponentParser::getFor(“properties”), [new            Requirement(“flags.properties”, Requirement::TYPE_BOOL, true])
  ->addParseComponent(ComponentParser::get(“item”))
  ->onComplete(function($data){ 
    $name = $data["nameIdentifier"]["name"];
    $identfier = $data["nameIdentifier"]["identifier"];
    $item = $data[“item”];
    $properties = $data[“properties”]; 
    };
```
