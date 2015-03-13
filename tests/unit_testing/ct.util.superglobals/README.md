# Unit tests for the *ct.util.superglobals* module.

## Test cases

For SG_Post, SG_Get and SG_Cookies :

- Base : 

1. check (isset, empty) on non existing key
2. check (isset, empty) on existing key but missing value
3. check (isset, trim, empty) on existing key and value " "
4. check (isset, empty) on existing key and value " "
5. check (isset, empty) on existing key and a non-empty value

- Array :

6. check (isset, empty) on non-empty array
7. check (isset, empty) on empty array

- Callback :

8. check (isset, empty, callback) on existing key and value verifying the callback
9. check (isset, empty, callback) on existing key and value not verifying the callback
10. check (isset, empty, callback) on empty array
11. check (isset, empty, callback) on array verifying the call back
12. check (isset, empty, callback) on array not verifying the call back 

