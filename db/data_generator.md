# Data generator 

## What is it?

The data generator is a program that generate dummy data to fill the database. This generator outputs SQL Insert Into query which has to be executed in a DBMS environment.

## How do I use it?

`python data_generator.py {Entity} {Number}` where **Entity** is the name of the requested entity, for instance, *User* or *Review*, and **Number** is the number of dummy entities you would like to generate.

Example #1:

`python data_generator.py User 1` - generates a single tuple of the User entity in SQL

Example #2:

`python data_generator.py Download 100` - generates 100 tuples of the Download entity in SQL

