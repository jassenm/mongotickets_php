
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Inventory Manager Login</title>
<meta name="description" content="">
<meta name="keywords" content="">

</head>

<body>
<form id="loginform"
      
        action="/ServiceLoginAuth.php" method="post">


	<h2>Sign in</h2>

<table>
	<tr>

		<td align="right"> Login: </td>
		<td> <input type="TEXT" name="login" id="login" size="18"/> </td>
	</tr>
	<tr>
		<td align="right"> Password: </td>
		<td> <input type="password" name="passwd" id="passwd" size="18"/> </td>
	</tr>

	<tr>
		<td align="right" valign="top">
		<input type="checkbox" name="PersistentCookie" id="PersistentCookie" value="yes" />
		<input type="hidden" name='rmShown' value="1" />
		</td>
		<td> Remember me on this computer.  </span></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		  <td> <input type="submit" value="Log in" /> </td>
	</tr>


</table>
</form>

</body>
</html>
  

