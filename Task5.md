### 1  Explain this code 
```php
Schedule::command('app:example-command')
->withoutOverlapping()
->hourly()
->onOneServer()
->runInBackground();
```
This Code schedules a command , uses the middleware withOutOverlapping() to prevent the jobs overlaps with other jobs in the queue, then the task is ran every hour , the onOneServer prevent that the job ran in multipleServers and finally the command is running on background mode 


### What is the difference between the Context and Cache Facades? Provide examples to illustrate your explanation.

The main difference between Context and Cache is the data persistance. The cache can be stored in redis or memcached. When you use the Context the data is lose after the request ends.
I have used the Cache Facade in my past jobs to optimize the response times, I stored data from tables with static information (a table with state names) to make faster queries and reduce the DB usage 
Otherwise the Context , can be use to store logs. 


### What's the difference between $query->update(), $model->update(), and $model->updateQuietly() in Laravel, and when would you use each?

1.- $query->update() this one is used when tou are making a query , and you want to do a bulk update , it doesn't require an instance of the model to be used and doesn't trigger the model events. 
This can be used to update the status of multiple users , for example change their status to unactive or remove privilegies. 

2.-  $model->update(), is used to update a single instance of a model. This is regular used to update a single product information or a client info. Also it triggers the event listeners 

3.- $model->updateQuietly() this also only updates a single instance of the model but it doesn't trigger the event listeners , so it can be used for example if a user updates normaly the profile it will recive a notification that the profile information was updated.
In the case of an administratror need to modify user information without noticing to the user it may be useful to run updateQuietly().
