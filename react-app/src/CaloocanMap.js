   import React from 'react';
   import { MapContainer, TileLayer, Marker, Popup } from 'react-leaflet';

   const CaloocanMap = () => {
     const position = [14.6396, 120.9822]; // Caloocan City (South)

     return (
       <MapContainer center={position} zoom={13} style={{ height: '400px', width: '100%' }}>
         <TileLayer
           attribution='&copy; <a href="https://osm.org/copyright">OpenStreetMap</a> contributors'
           url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
         />
         <Marker position={position}>
           <Popup>Caloocan City (South), Philippines</Popup>
         </Marker>
       </MapContainer>
     );
   };

   export default CaloocanMap;
   