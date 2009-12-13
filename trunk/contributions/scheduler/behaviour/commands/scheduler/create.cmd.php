<?php
Load::commands('base/createscheduler');

/**
 * Overload create command, to respect excusive paramter
 */
class CreateSchedulerCommand extends CreateSchedulerBaseCommand {
}