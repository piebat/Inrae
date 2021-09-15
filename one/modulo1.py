from types import new_class
from numpy import NaN, column_stack
import pandas as pd
from pandas.core.frame import DataFrame
import numpy as np
import json 

class MyImport(object):
    def __init__(self, *args):
        super(MyImport, self).__init__(*args)
        
    def importa(fn,head=1):
        df = pd.read_excel(fn,header=head)
        return df
    
    def importa_csv (fn, head=0):
        with open(fn,'r') as f:
            df = pd.read_csv(f,sep=";",header=head)
        return df

    def importa_sll_coordinates(args):
        with open(args,'r') as f:
            data = json.load(f)
            f.close()
        sll = {}
        for d in data['features']:
            sll[int(d['properties']['SLL_2011'])]= d['geometry']['coordinates']
        return sll

    def importaAteco(fn):
        df = pd.read_csv(fn,sep=";",header=0,index_col= 'codice',usecols=['codice', 'descrizione'])
        df.applymap(lambda x: str(x).strip())
        return df

