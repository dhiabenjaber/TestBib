<?php
namespace App\Constants;
abstract class Constants
{
    const NEVER_RETURNED = "Never Returned";
    const BORROWED = "Borrowed - Not Yet Returned";
    const REQUESTED = "Requested";
    const DECLINED = "Declined";
    const RETURNED = "Returned";
}
