import Footer from '@/components/Footer';
import Header from '@/components/Header';
import { Head } from '@inertiajs/react';
import { Container, Paper } from '@mui/material';
import React from 'react';
import { RedocStandalone } from 'redoc';

const DevelopersPage: React.FC = () => {
    return (
        <>
            <Head title="API Documentation - SMS X Numbers" />
            <Container maxWidth="lg" sx={{ py: 4, minHeight: '100vh', display: 'flex', flexDirection: 'column' }}>
                <Header />
                <Paper elevation={2} sx={{ mb: 4, flex: 1 }}>
                    <RedocStandalone
                        specUrl="/docs/swagger.yaml"
                        options={{
                            theme: {
                                colors: {
                                    primary: {
                                        main: '#1976d2',
                                    },
                                },
                            },
                        }}
                    />
                </Paper>
                <Footer />
            </Container>
        </>
    );
};

export default DevelopersPage;
