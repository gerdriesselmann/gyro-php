<?php
/**
 * @defgroup Tristate
 * @ingroup DB
 * 
 * A tristate DB field, allowing a user choice of YES, NO, and UNKNOWN.
 * 
 * While a tristate also can be achieved unsing a boolean field together with a NULL value, 
 * the Tristate DB field allows easier user input, since NULL is difficult to represent 
 * using default boolean user input widgets.  
 * 
 * @section Usage
 *
 * The tristate field is basically an Enum field, and can be used like this.
 * 
 * Use class DBFieldTristate in table declaration:
 * 
 * @code
 *   ...
 *   new DBFieldTristate('fieldname', Tristate::UNKNOWN, DBField::NOT_NULL),
 *   ...
 * @endcode
 * 
 * The Tristate class defines the three possible tristate values and offers helper functions. 
 */
