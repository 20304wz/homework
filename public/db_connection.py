import questionnaire.connector

def get_connection():
  try:
    connection = mysql.connector.connect(
      host = "localhost",
      user = "root",
      password = "zgy1356695061",
      database = "questionnaire"
    )
    return connection
  except mysql.connector.Error as err:
    print(f"Error: {err}")
