Properties
=====

### Comments
1. should corporation be a model with a migration. I.E. should it allow for more than one corperation? 
   * I decided that I would favour more than one corperation, so that it would be future proof, although in this coding 
     challenge only one is created (in the seeder)
2. The word "properties" is wide enough to include flats, villas, garages etc. Then I have decided to let the hierachy
   guide me. And since a building can contain one-or-many properties, then I have decided for "properties" to mean 
   flat/appartment.
3. When I was about to run out of time, I decided to complete the following quite fast, so they are written a little from
   the hip, and not tested, and are only meant as samples, ex. create should have a separate endpoint for each type of node. 
        0. Add a new node to the tree.
        1. Get all child nodes of a given node from the tree (only 1 layer of children).
        2. Change the parent node of a given node to another valid node.

### Installation

Please after pulling from the repository then please run:
1. composer install
2. npm install && npm build
3. Please update .env file according to local settings (for this I used SQLite)
4. php artisan migrate:refresh --seed 
