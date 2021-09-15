from django.shortcuts import render

# Create your views here.
from django.http import HttpResponse, response
from django.urls.conf import include
from django.template.response import  TemplateResponse
from numpy.core.numeric import NaN
from .models import Ateco, Sll, SllRel
from .modulo1 import MyImport as Mi
from neomodel import db
from neomodel.contrib.spatial_properties import NeomodelPoint
import json

# Create your views here.
template_home = 'one/home.html'
template_uno = 'one/uno.html'
template_due = 'one/due.html'
data_path = '/home/azureuser/PRIN/static/data/'

def sll(request):
    ateco2021 = Mi.importaAteco(data_path+'Ateco2007.csv') #import description of ATECO codes
    query = 'MATCH (n) DETACH DELETE n'
    db.cypher_query(query)

    df = Mi.importa_csv(data_path+'food.csv') #import the sll and Ateco nodes 
    d= df.set_index('code')
    punto = Mi.importa_sll_coordinates(data_path+'qgis3.geojson') #iimport the centroid coordinates of Sll 
    for j in d.columns: 
        if (j !=  'name'):
            i = "{}.{}".format(str(j)[0:2],str(j)[2:4])
            des = ateco2021.loc[i]['descrizione']
            Ateco(code=j, description=des).save() # Create the Ateco nodes
    for i in d.index:   
        try:
            sll = Sll(code=i, name = d.loc[i][0], lat=punto[i][1], lng=punto[i][0]).save() # Create the Sll nodes
        except:
            pass
        n=0
        for j in d.loc[i]: ## per ogni Sll aggiunge le relazioni con gli Ateco
            if n > 0:
                a = Ateco.nodes.get(code=int(d.columns[n]))
                sll.sll_ateco.connect(a,{'imprese':j})
            n=n+1
    ## Mostra i risultati
    return get_sll(request)
 
def index(request):
    return TemplateResponse(request, template_home, {'titolo': "Iniziamo"})

def get_sll(request):
    sll_list=Sll.nodes.all()
    ateco_list=Ateco.nodes.all()
    return TemplateResponse(request, template_home, {'sll_list':sll_list, 'ateco_list':ateco_list, 'msg': "Database Popolato"})

def del_all(request):
    query = 'MATCH (n) DETACH DELETE n'
    db.cypher_query(query)
    return TemplateResponse(request, template_home, {'msg': "Database Pulito"})

def get_map(request, fil=0):
    query = 'MATCH (n)-[r:contiene]->(m) WHERE r.imprese>' + str(fil) + ' RETURN { id: n.code, label:head(labels(n)), caption:n.name, lat:n.lat, lng: n.lng } as source, { id: id(m), label:head(labels(m)), caption:m.code } as target, { weight:log(r.imprese)/2, type:type(r), imprese: r.imprese} as rel' 
    results, meta = db.cypher_query(query)
    shapes={}
    with open(data_path+'SLL.json','r') as f:
        shapes = json.load(f)
    return TemplateResponse(request, template_due,{'nodi': results, 'filtro':fil, 'shapes':shapes}) 

def get_grafo(request, fil=0):
    query = 'MATCH (n)-[r:contiene]->(m) WHERE r.imprese>' + str(fil) + ' RETURN { id: n.code, label:head(labels(n)), caption:n.name, lat:n.lat, lng: n.lng } as source, { id: m.code, label:head(labels(m)), caption:m.code, description:m.description} as target, { weight:log(r.imprese)/2, type:type(r), imprese: r.imprese} as rel' 
    results, meta = db.cypher_query(query)
    shapes={}
    with open(data_path+'SLL.json','r') as f:
        shapes = json.load(f)
    return TemplateResponse(request, template_uno,{'nodi': results, 'filtro':fil, 'shapes':shapes}) 
