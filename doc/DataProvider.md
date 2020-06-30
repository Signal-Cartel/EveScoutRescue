# Data provider

Documentation about available data providers to be used with external systems or javascript. All providers return a JSon data set for further use.

## Type ahead search provider

- Location:
data/typeahead.php

- Purpose:
Provide prossible worm hole names based on the supplied input.

- Parameters:
    - 'query': the entered text to (wildcard) search for, e.g. 'J12'
    - 'type': can be 'system' (default) to search all possible systems or 'cache' to find active ESRC caches 

- Return:
A Json array:
     - with up to 5 possible matching systems: ["J120010", "J120103", "J120124", "J120131", "J120134"] 
     - or an empty list: []
       
## System and cache information for Allison Copilot

- Location:
data/copilot.php

- Purpose:
Provide system and cache information about the supplied wormhole system.

- Parameter:
      - cache: the ID of the system to get information for
      
- Return:
A Json hash:
       - with wormhole 'system' information: "System":"J101020","Class":"Class 1","Constellation":"Constellation 3","Region":"Region 2","DoNotSowUntil":null,"Notes":""}
       - with 'cache' information or false: "cache":{"System":"J101020","Location":"I","AlignedWith":"II","Status":"Healthy","ExpiresOn":"2017-04-30","InitialSeedDate":"2017-03-31","LastUpdated":"2017-03-31 10:56:57"}
       - an 'error' in case of a problem with an error message string

### Examples

#### System is not allowed to place a cache in

https://..../data/copilotextended.php?cache=J999999

        {
            "system": {
                "System": "J999999",
                "Class": "Class 4",
                "Constellation": "Constellation 183",
                "Region": "Region 19",
                "DoNotSowUntil": "2017-07-07",
                "Notes": ""
            },
            "cache": false
        }
        
#### System with a placed cache

https://..../data/copilotextended.php?cache=J999999

        {
            "system": {
                "System": "J999999",
                "Class": "Class 1",
                "Constellation": "Constellation 3",
                "Region": "Region 2",
                "DoNotSowUntil": null,
                "Notes": ""
            },
            "cache": {
                "System": "J999999",
                "Location": "I",
                "AlignedWith": "II",
                "Status": "Healthy",
                "ExpiresOn": "2017-04-30",
                "InitialSeedDate": "2017-03-31",
                "LastUpdated": "2017-03-31 10:56:57"
            }
        }

#### System without a cache

https://..../data/copilotextended.php?cache=J999999

        {
            "system": {
                "System": "J999999",
                "Class": "Class 1",
                "Constellation": "Constellation 3",
                "Region": "Region 2",
                "DoNotSowUntil": null,
                "Notes": ""
            },
            "cache": false
        }

#### Error response

https://..../data/copilotextended.php?cache=J999999

        {"error":"Invalid system: J999999"}

Watched trhice review this
