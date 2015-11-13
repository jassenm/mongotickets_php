// import java.sql.*;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.sql.Statement;
import java.sql.ResultSet;

public class Jdbc11 {
  public static void main(String args[]) {
    try {
      Statement stmt = null;
      ResultSet rs = null;

      //Class.forName("com.mysql.jdbc.Driver").newInstance();
      // Class.forName("Driver");
      // Class.forName("com.mysql.jdbc.Driver");
      String url =
            "jdbc:mysql://localhost:3306/mongo_tickets2";
      Connection con =
                     DriverManager.getConnection(
                                 url,"mongo_admin", "<password>");

      System.out.println("URL: " + url);
      System.out.println("Connection: " + con);
      stmt = con.createStatement();

      if (stmt.execute("SELECT EventName from Events ORDER BY EventName LIMIT 10")) {
          rs = stmt.getResultSet();
          System.out.println("Display all results:");
          while(rs.next()) {
            String str = rs.getString("EventName");
            System.out.println("\nEvent= " + str);
          }//end while loop
      }
    } // end try
    catch (SQLException ex) {
       // handle any errors
       System.out.println("SQLException: " + ex.getMessage());
       System.out.println("SQLState: " + ex.getSQLState());
       System.out.println("VendorError: " + ex.getErrorCode());
    }
// finally {

 //       if (rs != null) { 
   //         try {
     //           rs.close();
       //     } catch (SQLException sqlEx) { // ignore }

   //         rs = null;
     //   }
  //      if (stmt != null) { 
    //        try {
      //          stmt.close();
  //          } catch (SQLException sqlEx) { // ignore }

    //        stmt = null;
    //    }
   //     if (con != null) { 
    //        try {
    //            con.close();
    //        } catch (SQLException sqlEx) { // ignore }

    //        con = null;
   //     }
 //   } // end finally
  } //end main
 } //end class Jdbc11
