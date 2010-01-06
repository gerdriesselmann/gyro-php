<?php
/**
 * @defgroup DeleteDialog
 * 
 * Displays a dialog to approve deletion of instances. 
 *
 * @section Usage
 *
 * This module catches command execution for delete commands, and displays a 
 * dialog asking the user for approval. It works for delete commands serialized
 * and rendered by WidgetItemMenu only.
 * 
 * By default the approval dialog displays the message "Do you really want to delete this instance?".
 * You may change this for any given model type by placing a template named after the model in the 
 * directory view/templates/{lang}/deletedialog/message.
 */
