<?php
/**
 * @defgroup Tokens
 * @ingroup Models
 * 
 * Create tokens with a length of 40 characters, that are not guessable and guaranteed 
 * to be unique for at least 10 days.
 * 
 * Usage is simple:
 * 
 * @code
 * Load::models('tokens');
 * $token = Tokens::create_token();
 * @endcode
 * 
 * You also can pass your own seed:
 * 
 * @code
 * Load::models('tokens');
 * $token = Tokens::create_token('My Seed');
 * @endcode
 * 
 */
