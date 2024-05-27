# Neat Extras for FluentCRM

Extend FluentCRM with smart codes (merge codes) offering spintax support and random number generator.

## Spintax (spin syntax)

**Usage:**

```
{{nef.SPIN(Schrödinger’s Cat is {dead|alive}.)}}
```
This will return one of the following:

`Schrödinger’s Cat is dead.`

`Schrödinger’s Cat is alive.`

**Advanced usage:**

Nested structures are supported!

```
{{nef.SPIN(I {love {PHP|JavaScript|Python}|hate Ruby}.)}}
```
This will return one of the following:

`I love PHP.`

`I love Javascript.`

`I love Python.`

`I hate Ruby.`

Spintax uses https://github.com/bjoernffm/spintax library.

## Random number generator

**Usage:**

```
{{nef.RAND_NUM(0,100)}}
```

This will return a random number between 0 and 100 (inclusive).
