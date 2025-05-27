import { Head } from '@inertiajs/react';
import { Box, Container, Paper, Stack, Typography } from '@mui/material';
import React from 'react';

import Footer from '@/components/Footer';
import Header from '@/components/Header';

const HowItWorksPage: React.FC = () => {
    return (
        <>
            <Head title="How It Works - SMS X Numbers" />
            <Container maxWidth="md" sx={{ py: 4, minHeight: '100vh', display: 'flex', flexDirection: 'column' }}>
                <Header />
                <Paper elevation={2} sx={{ p: 3, mb: 4 }}>
                    <Typography variant="h4" gutterBottom>
                        How It Works
                    </Typography>

                    <Stack spacing={3}>
                        <Box>
                            <Typography paragraph>
                                SMS X Numbers helps you process large sets of phone numbers for SMS campaigns. Upload your numbers via file (CSV,
                                Excel, or text) or paste them directly into the text area. The system handles various number formats including
                                international (+263771234567) and local (0771234567), standardizing them all into a consistent format for your
                                campaign.
                            </Typography>
                        </Box>

                        <Box>
                            <Typography variant="h6" gutterBottom>
                                Understanding the Fields
                            </Typography>

                            <Typography variant="subtitle1" gutterBottom sx={{ mt: 2 }}>
                                File Upload
                            </Typography>
                            <Typography paragraph>The file upload option provides flexibility in importing your phone numbers:</Typography>
                            <ul>
                                <Typography component="li">Supports CSV, Excel (.xlsx, .xls), and text files</Typography>
                                <Typography component="li">CSV files should have one number per line or in a specific column</Typography>
                                <Typography component="li">Excel files can contain numbers in any column, the system will extract them</Typography>
                                <Typography component="li">Text files should have one number per line</Typography>
                            </ul>

                            <Typography variant="subtitle1" gutterBottom sx={{ mt: 2 }}>
                                Number Text Field
                            </Typography>
                            <Typography paragraph>The text field provides a direct way to input phone numbers:</Typography>
                            <ul>
                                <Typography component="li">Paste or type numbers directly into the field</Typography>
                                <Typography component="li">Each number must be on a new line</Typography>
                                <Typography component="li">Supports copying directly from spreadsheets or documents</Typography>
                                <Typography component="li">Automatically cleans up common formatting issues (spaces, dashes, etc.)</Typography>
                            </ul>

                            <Typography variant="subtitle1" gutterBottom sx={{ mt: 2 }}>
                                Country Code
                            </Typography>
                            <Typography paragraph>The country code is essential for proper number formatting and validation:</Typography>
                            <ul>
                                <Typography component="li">
                                    Determines how local numbers (starting with 0) are converted to international format
                                </Typography>
                                <Typography component="li">Used to validate the length and pattern of phone numbers</Typography>
                                <Typography component="li">Ensures all numbers are standardized to the same international format</Typography>
                            </ul>

                            <Typography variant="subtitle1" gutterBottom sx={{ mt: 2 }}>
                                Chunk Size
                            </Typography>
                            <Typography paragraph>The chunk size controls how your numbers are split for processing:</Typography>
                            <ul>
                                <Typography component="li">Range: 100 to 10,000 numbers per chunk</Typography>
                                <Typography component="li">Smaller chunks (e.g., 1,000) are ideal for SMS campaigns with rate limits</Typography>
                                <Typography component="li">Larger chunks (e.g., 10,000) work well for bulk processing</Typography>
                                <Typography component="li">Each chunk maintains the original order of numbers</Typography>
                            </ul>
                        </Box>

                        <Box>
                            <Typography variant="h6" gutterBottom>
                                Processing Steps
                            </Typography>
                            <ol>
                                <Typography component="li">Upload your numbers file or paste them in the text area</Typography>
                                <Typography component="li">Select the country code for local format numbers</Typography>
                                <Typography component="li">Choose your preferred chunk size (100-10000 numbers per chunk)</Typography>
                                <Typography component="li">Click "Upload and Process" to start</Typography>
                                <Typography component="li">Get your processed numbers split into manageable chunks</Typography>
                            </ol>
                        </Box>

                        <Box>
                            <Typography variant="h6" gutterBottom>
                                How It All Works Together
                            </Typography>
                            <Typography paragraph>When you submit your numbers, the system follows these steps:</Typography>
                            <ol>
                                <Typography component="li">Your input (file or text) is parsed to extract individual phone numbers</Typography>
                                <Typography component="li">
                                    Each number is validated and standardized using the selected country code:
                                    <ul>
                                        <Typography component="li" sx={{ mt: 1 }}>
                                            Local numbers have the country code prefix added
                                        </Typography>
                                        <Typography component="li">International numbers are verified against country patterns</Typography>
                                        <Typography component="li">Invalid numbers are filtered out but counted in results</Typography>
                                    </ul>
                                </Typography>
                                <Typography component="li">Valid numbers are grouped into chunks based on your specified size</Typography>
                                <Typography component="li">Each chunk is saved and made available for download</Typography>
                                <Typography component="li">You receive a summary showing total, valid, and invalid numbers</Typography>
                            </ol>
                            <Typography paragraph sx={{ mt: 2 }}>
                                The processed files remain available for 30 days, allowing you to download individual chunks or the complete set of
                                numbers as needed for your SMS campaigns.
                            </Typography>
                        </Box>
                    </Stack>
                </Paper>
                <Footer />
            </Container>
        </>
    );
};

export default HowItWorksPage;
