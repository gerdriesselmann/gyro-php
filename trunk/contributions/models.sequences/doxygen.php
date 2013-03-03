<?php
/**
 * @defgroup Sequences
 * @ingroup Models
 * 
 * Allow generation of sequences. Sequences guarantee a continues range of numbers,
 * e.g. for invoice numbering. Other than auto incrementing IDs, sequences do not
 * have gaps (if used properly). A gap in auto incrementing e.g. may occur if an
 * insert is rolled back.
 *
 * Sequences are organised in slots, every slot is incremented separately, so you may
 * manage sequences for different usages.
 * *
 * Sequences use the FOR UPDATE SQL clause, so retrieving a sequence value blocks
 * any further retrieval (from the same slot), as long as the sequence is commited.
 *
 * Usage is simple:
 * 
 * @code
 * Load::models('sequences');
 * $sequence = Sequences::next('invoices'); // Invoices is the slot name
 * $invoice_number = $sequence->current();
 * // Do something here
 * ...
 * // When finished, commit the sequence. This is important!
 * $sequence->commit()
 * @endcode
 * 
 * In a command chain, you may want to use a command that gets appends at the end
 *
 * @important Always use transactions, when composing commands and using sequences
 *
 * @code
 * Load::models('sequences');
 * $sequence = Sequences::next('invoices'); // Invoices is the slot name
 * $invoice_number = $sequence->current();
 * // Do something here
 * ...
 * // When finished, commit the sequence. This is important!
 * $this->append($sequence->create_commit_command());
 * @endcode
 */
