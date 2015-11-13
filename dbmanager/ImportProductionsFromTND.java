// import java.sql.*;
package org.apache.axis2.tn.v3a;
import org.apache.axis2.tn.v3a.TNWebServiceStub;
import org.apache.axis2.tn.v3a.TNWebServiceStub.GetEvents;



import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.sql.Statement;
import java.sql.ResultSet;

public class ImportProductionsFromTND.java {
        public static void main(String[] args) throws Exception {

      Statement stmt = null;
      ResultSet rs = null;
      Connection con = null;
      String url = null;
    try {
               TNWebServiceStub tnStub = new TNWebServiceStub();
                TNWebServiceStub.GetEvents request = new TNWebServiceStub.GetEvents();
                request.setWebsiteConfigID(4589);
                System.out.println("\n Sending GetEvents Request.\n");
                TNWebServiceStub.GetEventsResponse resp;
                resp = tnStub.GetEvents(request);
                System.out.println("\nReceived GetEventsResponse " + resp.getGetEventsResult());


      url = "jdbc:mysql://localhost:3306/mongo_tickets2";
      con = DriverManager.getConnection(
               url,"mongo_admin", "<password>");


      stmt = con.createStatement();

      stmt.executeUpdate("DROP TABLE IF EXISTS TNDProductions_temp");
      stmt.executeUpdate(
          "CREATE TABLE TNDProductions_temp(" +
                "ProductionID INT NOT NULL," +
                "ProductionName CHAR(100)," +
                "ProductionDate DATETIME," +
                "DisplayDate CHAR(100)," +
                "ChildCategoryID INT," +
                "GrandchildCategoryID INT," +
                "ParentCategoryID INT," +
                "VenueID INT," +
                "VenueName CHAR(100)," +
                "City CHAR(100)," +
                "StateProvince CHAR(100)," +
                "StateProvinceID INT," +
                "MapURL CHAR(150)," +
                "VenueConfiguration INT ," +
                "PRIMARY KEY (ProductionID,EventID))"
      );



      TNWebServiceStub.ArrayOfEvent ar = new TNWebServiceStub.ArrayOfEvent();
      ar = resp.getGetEventsResult();
      System.out.println("\n " + ar.getEvent());
      TNWebServiceStub.Event[] ev =  ar.getEvent();

      System.out.println("\nNumber of performers: " + ev.length);
      for (int i = 0; i < ev.length; i++)
      {
            stmt.executeUpdate(
            "INSERT INTO TNDProductions_temp ( ProductionID,ProductionName,ProductionDate,DisplayDate,ChildCategoryID,GrandchildCategoryID,ParentCategoryID,VenueID,VenueName,City,StateProvince,StateProvinceID,MapURL,VenueConfiguration) " + 
            "values (" + 
                 ep[i].getID() + ",'" + ep[i].getName() + "'," +
                 ep[i].getPerformerName() + "')"
            );
      } // end for


//      if (stmt.execute("SELECT * from TNDEventPerformers")) {
  //        rs = stmt.getResultSet();
    //      System.out.println("Display all results:");
      //   while(rs.next()) {
        //    String str = rs.getString("EventName");
      //      System.out.println("\nEvent= " + str);
      //    }//end while loop
   //   }
      con.close();
    } // end try
    catch (SQLException ex) {
       // handle any errors
       System.out.println("SQLException: " + ex.getMessage());
       System.out.println("SQLState: " + ex.getSQLState());
       System.out.println("VendorError: " + ex.getErrorCode());
    } // end catch
 finally {

        if (rs != null) { 
            try {
                rs.close();
            } catch (SQLException sqlEx) {  
                  // ignore 
            }

            rs = null;
        }
        if (stmt != null) { 
            try {
                stmt.close();
            } catch (SQLException sqlEx) { 
                  // ignore 
            }

            stmt = null;
       }
        if (con != null) { 
            try {
                con.close();
            } catch (SQLException sqlEx) { 
                // ignore 
            }

            con = null;
        }
   } // end finally
  } //end main
 } //end class
