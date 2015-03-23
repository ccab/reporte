El UsersBundle permite la gestion de los usuarios del sistema, se integra con 
FOSUserBundle para el almacenamiento en BD de dichos usuarios, pero ademas 
logra la autenticacion y autorizacion de los usuarios basado en el DC.
Cuando un usuario del dominio entra sus credenciales primero se busca si existe 
en el DC, una vez autenticado se extraen todos los grupos a los q pertenece
para poder identificar su area y sus roles, esta informacion se actualiza en BD
para mantener los datos frescos, por lo tanto se mantiene una copia lo mas 
actualizada posible del usuario.