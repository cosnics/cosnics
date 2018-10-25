We chose for a denormalised solution in this project due to projected performance issues. In this document you'll
find the problem and the reasons why we have chosen for this datamodel. 

# The use cases

The notification system was created to create notifications on different levels. Both on a global level 
(for all noticiations) and on a more contextual level (e.g. for a specific course or for all the assignments). 

As an example we are using notifications for new assignment entries. We want to be able to show our notifications
on three levels:

1) The global level. This is the place where all the notifications for a specific user are shown 
2) A specific block on the home page showing only the assignments. (This is the hardest part due to being user AND 
context filtered). 
3) A list in a specific assignment showing all the notifications for that specific assignment.

# The problem

In an optimal database you would have a notification table with all the data about the notification itself 
(url, description), a relation between users and notifications, a context (filter) table and a relation between
contexts and notifications. 

The tables in an normalised structure could look like this. (simplified version to describe the problem)
 
### Notification
 
| id      | url     | description |  
|---------|:-------:|:-----------:|

### UserNotification

| id      | userId    | notificationId |  
|---------|:---------:|:--------------:|

### Context / Filter

| id      | path    | description |  
|---------|:-------:|:-----------:|

### NotificationContext

| id      | contextId | notificationId |  
|---------|:---------:|:--------------:|

The issue is very visible when trying to query the results for use case 2 (both selecting user and context). This
would result in a query which looks a bit like this:

``` 
SELECT * FROM UserNotification UN
JOIN Notification N on N.id = UN.notificationId
JOIN NotificationContext NC on N.id = NC.notificationId
WHERE UN.userId = 2 AND NC.contextId = 5;
```

The problem with this query is that there is a filtering on two tables that are joined together. Under normal 
circumstances this wouldn't pose a real problem. But due to the nature of the UserNotification table and the amount
of expected records in that table the join size would be extreme which would result in slow queries and possibly
locked tables. 

# The Solution

We have chosen to denormalize the relation between the context and a notification 
and the relation between the user and the notification. We will store every possible context for a user in his 
relation table. Extremely simplified it would look like this:

| id      | userId    | contextId      | notificationId | date  |   
|---------|:---------:|:--------------:|:--------------:|:-----:|

The query would be simplified a lot. The join would only be used to retrieve the actual data and not for filtering.
Thus increasing the performance for the retrieved notifications. At the cost of storing a multitude of data. 

``` 
SELECT * FROM UserNotification UN
JOIN Notification N on N.id = UN.notificationId
WHERE UN.userId = 2 AND UC.contextId = 5
ORDER BY DATE DESC LIMIT 0, 20;
```

# The Compromises

As this is a very complex problem the solution is not bullet proof in every use case. Every question depending
notifications should therefor keep this new architecture in mind. A few of the compromises we have to make are:

### Notifications not activities

Due to performance issues and complexity with user filters we can never use notifications as an activity log. 
Notifications will always be user bound, no matter what. This means that if you are newly subscribed to a course
for example, you'll only see notifications from that point on, you'll never see older notifications. Even so
when the notifications are shown in the course or e.g. in an assignment of the course. This also means that
notifications can only be marked as read / viewed by the current user. Other users viewing their notifications
will not impact your notifications. 

### Context Limits

We should be cautious with adding new context to a notification due to the denormalised data because every 
context would be multiplied with the amount of targeted users of that notification. Therefore we should consider
if creating a context is really necessary to solve a certain use case, or if we could somehow calculate / convert
the question / data to a context that is already stored in the database. Thus limiting the amount of records
that need be queried and reducing the complexity and performance issues. 

# Issues

In normal queries a notification could be shown multiple times if it is connected to multiple filters on which 
we are selecting. To prevent this we should use a **GROUP BY** statement on the notification content. 