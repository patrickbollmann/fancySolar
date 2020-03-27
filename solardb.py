"""solardb.py: Establishes a connection to your online database"""

__author__      = "Patrick Bollmann"
__email__      = "pbollman@mail.upb.de"

import pymysql
import logging

# DataBase Connection
class DataBase:
    con = None

    def __init__(self):
        self.connect()

    def connect(self):
        logging.info("Verbinde mit Datenbank...")
        try:
            self.con = pymysql.connect(host="hostaddress of database",
                                        user="user",
                                        passwd="pass",
                                        db="solar",
										use_unicode=True,
										charset="utf8")
        except pymysql.DatabaseError as e:
            logging.error("Verbindung mit Datenbank konnte nicht hergestellt werden! " + str(e))
            return False
        else:
            logging.info("Mit Datenbank verbunden!")
            return True

    def query(self, sql):
        try:
            cursor = self.con.cursor(pymysql.cursors.DictCursor)
            cursor.execute(sql)
            self.con.commit()
        except pymysql.OperationalError as e:
            logging.warning("Die Query konnte nicht ausgeführt werden! " + str(e))
            if (self.connect()):
                cursor = self.con.cursor(pymysql.cursors.DictCursor)
                cursor.execute(sql)
                self.con.commit()

        return cursor
        
    def disconnect(self):
        logging.info("Schließe Datenbankverbindung...")
        if (self.con != None):
            self.con.close()
        logging.info("Datenbankverbindung geschlossen!")
