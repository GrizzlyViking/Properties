Property API exercise
====

This exercise is meant to show a command of the technologies we use, as well as a keen head for problem solving and
tackling abstract challenges. We ask to have the challenge returned within a maximum of **three days** of it being assigned.

It should take approximately 3 hours to finish this task, but let us know if you need more time.

The task is meant to challenge you to show API skills and backend system design. There is also a second, **optional**, frontend
bonus task designed to evaluate how well you can consume the API you have just created in a good way.

Please use PHP to solve this task, using a framework of your choice but preferably using Laravel.

If you do not have time to finish the task, please leave comments describing how you would have implemented the missing
parts.

# Creating the API

In the PropTech business we need to model the relationships between corporations, buildings and properties
(rental units) so that we can accurately reflect reality and give our customers a structure they feel comfortable
relating to. In additional we have tenancy periods and tenants that connect individual renters with properties.

This structure ("taxonomy" if you will) is what you will be modeling in the first part of this challenge!

We have our root node (only one, representing the Corporation) and several child nodes.
Each of these nodes may have its own children, but there are strict rules as to which parent types may preside over which child types.

It can be structured as something like this:
```
        root
       /    \
      a      b
      |
      c
    / 	\
   d     e
```

## We need endpoints that will serve these basic operations :

0. Add a new node to the tree.
1. Get all child nodes of a given node from the tree (only 1 layer of children).
2. Change the parent node of a given node to another valid node.

Each node should have the following data:

0. Node identifier.
1. Node name.
2. Relationship to the parent node.
3. The height of the node (in the example above `height(root)=0` and `height(a)=1`).
4. The nodes should have a type from the following list: Corporation, Building, Property, Tenancy Period, Tenant.
4. Buildings should have an extra field specifying the zip code they are located in.
5. Properties should have an extra field specifying the monthly rent of the property.
6. Tenancy periods should have an extra field determining whether they are active or not.
7. Tenants should have an extra field showing when they moved in.

The nodes must observe these rules:

1. Properties can only have Buildings as parents.
2. Tenancy Periods can only have Properties as parents.
3. Tenants can only have Tenancy Periods as parents.
4. Only one Tenancy Period can be active in a Property at a time.
5. A Tenancy Period can have a maximum of 4 tenants at any time.

In addition, try to consider the following things:

1. Authentication and authorization.
2. Validation and API responses.
3. Best practices.

# Optional Task

If you have a lot of time left over after creating the API, feel free to complete this **completely optional** assignment.

Now that you have an API to work with, let's create a quick and dirty UI we can use to move nodes around.

### We need two things:

0. A view that shows the hierarchy (can be absolutely quick and dirty).
1. A feature that allows us to move one node to another parent node by calling the "change parent node" endpoint. Make sure to take all restrictions into account and have the system respond appropriately.

A few notes:

0. Bonus points if each node is represented by an independent UI component.
1. Good structure is more important than styling (don't be afraid to go ugly, as long as your code is sound!)

# How to hand it in

Please upload your solution to a git repository (BitBucket, GitLab or GitHub) and provide a README.md file with
documentation, including how to run your solution, then send us a link to the repo (make sure it's public or otherwise
accessible).

Feel free to describe what you would have improved in your solution if more time was given!

