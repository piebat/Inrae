from logging import PercentStyle
from django.urls import path
from django.conf.urls.static import static
from django.conf import settings

from . import views
app_name = 'one'
urlpatterns = [
    path('', views.index, name='index'), ## home page application one
    path('get_sll/', views.get_sll, name='read'), ## List the SLL on neo4j
    path('sll/',views.sll, name='populate'), ## polpola tutto il neo4j
    path('delete/',views.del_all, name='delete'), ## svuota il neo4j
    path('grafo/<int:fil>/',views.get_grafo, name='grafo'), ## visualizza il grafo 3d e la mappa
    path('map/<int:fil>/', views.get_map, name='map'), ## visualizza solo la mappa
]