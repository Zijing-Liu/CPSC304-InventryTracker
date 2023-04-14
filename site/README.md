# How to Add Pages to Our Front-end

## Summary

This document provides some simple step-by-step guidelines for adding more pages/queries to our front-end system.

## Step 1: Create a new .php file for your page

Create this in the top-level `/site` directory.

- You can start from the `template.php` file.

## Step 2: Add this page to multi-page navigation

1. Navigate to the `headernav.html` file in the `site/html/` directory.
2. Add a hyperlink object for your `.php` file to the `pagelink` `<div>`.

## Step 3: Create a `<form>` for user input

1. Think about the query/queries you want to implement on this page and what information you will need from the user.
2. Create an HTML `<form>` to obtain this information from the user. Don't forget to set the `action` of the form to the PHP file to the current file instead of `template.php`.
    - the form should be located in the `userinput` `<div>`.
    - use the `<input>` `type` to control what the user can enter - for example, `type=number` for integers.

## Step 4: Implement the SQL query

### Step 4.1: Parameters

- The user parameters are located in the `_GET` superglobal.
- Make sure every parameter is a sensible value.
  - For example, set nonnegative integers to some default value if the user inputted something invalid.
  - You can use the coalesce operator `??` to set a non-existent parameter to a default value. For example, if 'selection' is the resulting choice of some radio buttons and it is possible that the user does not make any selection, the following line of code:

    > `$selection = _GET['selection] ?? null;`

    will set the variable `$selection` to the result of the user selection if it exists - but if the user didn't select anything, `null` will be assigned.
- By the end of this step, you should have a set of variables that fully determines what action the user would like to take (which query to run), and the parameters (if any) for that query.

### Step 4.2: Query Configuration

- During this step, we aim to produce 3 PHP variables:
    1. `$query` : this contains the actual SQL query to run.
    2. `$arguments` : this is an associative array containing the values for the placeholders in `$query`. Can be `null` if there are no placeholders.
    3. `$description` : a string that describes the query.
- Using conditionals, we can construct the SQL query from fragments (see `stock.php` for an example of this), if there are parts of the query that are 'optional' and only present based on user input.
- User parameters can be inserted either using:
  - placeholders in the query string (of form `:placeholder`, `stock.php` uses this method)
  - directly inserting parameters into the string (using php string variable insertion. see `product_search.php`, which uses this)

### Step 4.3: Query Execution

Extremely simple.

- If `$query` and `$arguments` are correctly created in the previous section, you need only add one line of code for this:

  `include "scripts/exec_query.php";`

- This will produce a `$data` variable, which is an array containing all the rows returned by the query.
- Note: For performance reasons, try to limit the number of rows returned using `LIMIT` clauses if you forsee the query having the potential to return a lot of rows.

See `stock.php`, `product_search.php`, and `template.php` for examples. See `scripts/exec_query.php` for additional documentation.

### Step 4.4: Display of Data

Also simple.

- If `$data` and `$description` been created correctly, simply add a header that displays `$description` for that.
- For `$data`, if your result is designed to be a single row, then handle it as you wish.
- For multiple rows, you often want to display it as a table. This can be done by including `scripts/display_data.php`, which has a function `construct_table($data, $arr_columns, $header_names, $classname)` that returns the HTML for the table, which you can then directly echo onto the page.
- Using the same parameters you obtained in Step 4.1, you can choose to display a different table to the user - see `stock.php` for an example of this.

See `stock.php` and `product_search.php` for examples. See `scripts/display_data.php` for additional documentation on `construct_table()`.

## Step 5: Profit

That's it
