<?php

namespace App\Constants;


class StatementConstants
{

    const TRANSACTION_TYPE_BANK_STATEMENTS = 1;

    const PENDING = "pending";
    const PROCESSING = "processing";
    const COMPLETED = "completed";
    const FAILED = "failed";

    const TRANSACTION_PENDING = "pending";
    const TRANSACTION_CONFIRMED = "confirmed";

    const DEBIT = 1;
    const CREDIT = 0;

}
