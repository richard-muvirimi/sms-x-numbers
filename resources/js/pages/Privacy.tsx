import Footer from '@/components/Footer';
import Header from '@/components/Header';
import { Head } from '@inertiajs/react';
import { Box, Container, Paper, Typography } from '@mui/material';
import React from 'react';

const PrivacyPage: React.FC = () => {
    return (
        <>
            <Head title="Privacy Policy - SMS X Numbers" />
            <Container maxWidth="lg" sx={{ py: 4, minHeight: '100vh', display: 'flex', flexDirection: 'column' }}>
                <Header />
                <Paper elevation={2} sx={{ p: 4, mb: 4, flex: 1 }}>
                    <Typography variant="h4" component="h1" gutterBottom>
                        Privacy Policy
                    </Typography>

                    <Box sx={{ mt: 4 }}>
                        <Typography variant="h5" gutterBottom>
                            Data Collection and Usage
                        </Typography>
                        <Typography paragraph>
                            SMS X Numbers processes phone numbers for validation and formatting. We collect and process the following data:
                        </Typography>
                        <ul>
                            <Typography component="li">Uploaded phone number files</Typography>
                            <Typography component="li">Individual phone numbers entered through the interface</Typography>
                            <Typography component="li">Country codes selected for validation</Typography>
                        </ul>

                        <Typography variant="h5" gutterBottom sx={{ mt: 4 }}>
                            Data Storage
                        </Typography>
                        <Typography paragraph>
                            Files and phone numbers are stored temporarily for processing and are automatically deleted after 30 days. We do not
                            permanently store any phone numbers or contact information beyond this period.
                        </Typography>

                        <Typography variant="h5" gutterBottom sx={{ mt: 4 }}>
                            Third-Party Services
                        </Typography>
                        <Typography paragraph>
                            We use country.io API to fetch country codes and phone number prefixes. This service is used only for reference data and
                            no personal information is shared with it.
                        </Typography>

                        <Typography variant="h5" gutterBottom sx={{ mt: 4 }}>
                            Security
                        </Typography>
                        <Typography paragraph>
                            All data is processed securely and is not shared with any third parties. Files are stored with unique identifiers and are
                            only accessible through secure, temporary URLs.
                        </Typography>

                        <Typography variant="h5" gutterBottom sx={{ mt: 4 }}>
                            Contact
                        </Typography>
                        <Typography paragraph>
                            If you have any questions about this privacy policy or our handling of your data, please contact the SMS X support team.
                        </Typography>
                    </Box>
                </Paper>
                <Footer />
            </Container>
        </>
    );
};

export default PrivacyPage;
