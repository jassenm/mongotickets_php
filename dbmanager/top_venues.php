


SELECT *, COUNT(Productions.ProductionID) as num_prods FROM Venues inner join Productions on (Venues.VenueID = Productions.VenueID) inner join Events on (Events.EventID = Productions.EventID) WHERE EventTypeID=4 GROUP BY Venues.VenueID ORDER BY num_prods DESC LIMIT 10;

