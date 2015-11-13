<?php

                                        $tickets[] = array("TicketID" => "2309234",
                                                        "Available" => "3",
                                                        "SeatSection" => "30",
                                                        "SeatRow" => "4",
                                                        "TicketPrice" => "$30",
                                                        "Descr" => "good seats"
                                                        );

                                        $tickets[] = array("TicketID" => "1209298",
                                                        "Available" => "4",
                                                        "SeatSection" => "GA",
                                                        "SeatRow" => "4",
                                                        "TicketPrice" => "$10",
                                                        "Descr" => "seats"
                                                        );
                                        $tickets[] = array("TicketID" => "0405871",
                                                        "Available" => "1",
                                                        "SeatSection" => "0",
                                                        "SeatRow" => "8",
                                                        "TicketPrice" => "$99",
                                                        "Descr" => "seats 3"
                                                        );
                                        $tickets[] = array("TicketID" => "8473607",
                                                        "Available" => "1",
                                                        "SeatSection" => "0",
                                                        "SeatRow" => "12",
                                                        "TicketPrice" => "$93",
                                                        "Descr" => "seats 3"
                                                        );


// Obtain a list of columns
foreach ($tickets as $key => $row) {
    $SeatSection[$key]  = $row['SeatSection'];
    $TicketPrice[$key] = $row['TicketPrice'];
    $SeatRow[$key] = $row['SeatRow'];

}

#array_multisort($TicketPrice, SORT_ASC, $tickets);
#array_multisort($SeatSection, SORT_ASC, $tickets);
array_multisort($SeatSection, SORT_DESC, $TicketPrice, SORT_ASC, $tickets);
print_r($tickets);
?> 

?>
