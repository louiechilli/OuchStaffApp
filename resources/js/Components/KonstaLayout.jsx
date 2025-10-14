import { App, Page, Navbar, Block, Button, List, ListItem } from 'konsta/react';

export default function KonstaLayout({ children, title = 'Laravel + Konsta' }) {
    return (
        
            
                
                {children}
            
        
    );
}