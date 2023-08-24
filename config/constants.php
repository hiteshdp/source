<?php
    return [
        'wellkasa_url' => 'https://wellkasa.com',
        // start date range for date of birth selection constant
        'startDateRange' => date("m/d/Y", strtotime("-100 year", time())), 
        // end date range for date of birth selection constant
        'endDateRange' => date("m/d/Y", strtotime("-18 year", time())), 
        'Footer_TRC_URL' => 'http://www.trchealthcare.com/',
        'wellkabinet_intro' => 'https://calendly.com/d/2c4-fvp-dz2/wellkabinet-intro',
        'MidasTestId' => '10000',
        'HitSixTestId' => '10002',
        'MigraineRoutes' => ['signup','migrainemight','about-migraine-might'],
        'MigraineQuizIds' => [ '10000', '10002']
    ];
?>