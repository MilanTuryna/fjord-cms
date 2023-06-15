<?php


namespace App\Forms;


interface FormOption
{
    const OPTION_NOTE = "option_note";
    const DELETE_LINK = "___delete_link___";

    const BOTTOM_LINE = "___bottom_line___";
    const UPPER_LINE = "___upper_line___";

    const BOTTOM_BR = "___bottom_br___";
    const UPPER_BR = "___upper_br___";

    const BACKGROUND = "___background___";
    const GROUP_TITLE = "___group_title___";

    const ALERT_SUCCESS = "___alert_success___";
    const ALERT_ERROR = "___alert_error___";

    const BUTTON_DANGER = "___button_danger___";
    const BUTTON_SUCCESS = "___button_success___";

    const FULL_WIDTH = "___full_width___";

    const MULTIPLIER_PARENT = "___multiplier_parent___";
}