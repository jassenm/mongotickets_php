// import java.sql.*;
package org.apache.axis2.tn.v3a;
import org.apache.axis2.tn.v3a.TNWebServiceStub;
import org.apache.axis2.tn.v3a.TNWebServiceStub.GetEventPerformers;



import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.sql.Statement;
import java.sql.ResultSet;

public class ImportEventPerformersFromTND {
        public static void main(String[] args) throws Exception {

      Statement stmt = null;
      ResultSet rs = null;
      Connection con = null;
      String url = null;
    try {
               TNWebServiceStub tnStub = new TNWebServiceStub();
                TNWebServiceStub.GetEventPerformers request = new TNWebServiceStub.GetEventPerformers();
                request.setWebsiteConfigID(4589);
                // request.setEventID("814617");
                System.out.println("\n Sending GetEventPerformers Request.\n");
                TNWebServiceStub.GetEventPerformersResponse resp;
                resp = tnStub.GetEventPerformers(request);
                System.out.println("\nReceived GetEventPerformersResponse " + resp.getGetEventPerformersResult());


      url = "jdbc:mysql://localhost:3306/mongo_tickets2";
      con = DriverManager.getConnection(
               url,"mongo_admin", "<password>");


      stmt = con.createStatement();

      stmt.executeUpdate("DROP TABLE IF EXISTS TNDEventPerformers");
      stmt.executeUpdate(
          "CREATE TABLE TNDEventPerformers (" +
                "ProductionID INT NOT NULL," +
                "EventID INT," +
                "EventName CHAR(100)," +
                "PRIMARY KEY (ProductionID))"
      );


      TNWebServiceStub.ArrayOfEventPerformer ar = new TNWebServiceStub.ArrayOfEventPerformer();
      ar = resp.getGetEventPerformersResult();
      System.out.println("\n " + ar.getEventPerformer());
      TNWebServiceStub.EventPerformer[] ep =  ar.getEventPerformer();

      System.out.println("\nNumber of performers: " + ep.length);
      for (int i = 0; i < ep.length; i++)
      {
            stmt.executeUpdate(
            "INSERT INTO TNDEventPerformers (ProductionID,EventID,EventName) " + 
            "values (" + ep[i].getEventID() + "," + ep[i].getPerformerID() + ",'" +
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
