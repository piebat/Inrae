from django.db import models

from typing import List
from neomodel import (config, StructuredNode, StringProperty, IntegerProperty,
    UniqueIdProperty, RelationshipTo, StructuredRel, DateTimeProperty)
from neomodel.contrib.spatial_properties import NeomodelPoint
from neomodel.properties import FloatProperty

config.DATABASE_URL = 'bolt://neo4j:stupidoFlanders@localhost:7687'
# Create your models here.
class SllRel(StructuredRel):
    imprese = IntegerProperty()
    
class Ateco(StructuredNode):
    code = IntegerProperty(unique_index=True, required=True)
    description = StringProperty()
    url = StringProperty()

class Sll(StructuredNode):
    code = IntegerProperty(unique_index=True, required=True)
    name = StringProperty()
    sll_ateco = RelationshipTo(Ateco, 'contiene', model=SllRel)
    #location = NeomodelPoint((0,0),crs='wgs-84')
    lat = FloatProperty()
    lng = FloatProperty()




