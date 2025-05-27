import { Head, router } from '@inertiajs/react';
import {
    CloudDownload as CloudDownloadIcon,
    CloudUpload as CloudUploadIcon,
    Numbers as NumbersIcon,
    Public as PublicIcon,
    Upload as UploadIcon,
} from '@mui/icons-material';
import {
    Alert,
    AlertTitle,
    Box,
    Button,
    Container,
    FormControl,
    InputLabel,
    LinearProgress,
    Link,
    MenuItem,
    Paper,
    Select,
    Stack,
    TextField,
    Typography,
} from '@mui/material';
import React, { FormEvent, useState } from 'react';

import Footer from '@/components/Footer';
import Header from '@/components/Header';
import { fetchApi } from '@/lib/api';
import { CountriesResponse, Country, UploadResponse } from '@/types/api';

const UploadPage: React.FC = () => {
    React.useEffect(() => {
        const fetchCountries = async () => {
            try {
                const data = await fetchApi<CountriesResponse>('/api/v1/countries');
                setCountries(data.data);
                if (data.data.length > 0) {
                    setCountryCode(data.data[0].code);
                }
            } catch (err) {
                setError(err instanceof Error ? err.message : 'Failed to load countries');
            } finally {
                setLoadingCountries(false);
            }
        };

        fetchCountries();
    }, []);
    const [file, setFile] = useState<File | null>(null);
    const [textContent, setTextContent] = useState('');
    const [countryCode, setCountryCode] = useState('');
    const [countries, setCountries] = useState<Country[]>([]);
    const [loadingCountries, setLoadingCountries] = useState(true);
    const [chunkSize, setChunkSize] = useState(1000);
    const [error, setError] = useState('');
    const [processing, setProcessing] = useState(false);

    const handleSubmit = async (e: FormEvent) => {
        e.preventDefault();
        setError('');
        setProcessing(true);

        const formData = new FormData();
        if (file) {
            formData.append('file', file);
        } else if (textContent) {
            formData.append('numbers', textContent);
        } else {
            setError('Please provide either a file or text content');
            setProcessing(false);
            return;
        }

        formData.append('country_code', countryCode);
        formData.append('chunk_size', chunkSize.toString());

        try {
            const data = await fetchApi<UploadResponse>('/api/v1/files/process', {
                method: 'POST',
                data: formData,
                // Axios will automatically set the correct Content-Type for FormData
            });

            router.visit(`/uploads/${data.data.id}`);
            setProcessing(false);
        } catch (err) {
            setError(err instanceof Error ? err.message : 'An error occurred');
            setProcessing(false);
        }
    };

    return (
        <>
            <Head title="Upload Phone Numbers - SMS X Numbers" />
            <Container maxWidth="md" sx={{ py: 4, minHeight: '100vh', display: 'flex', flexDirection: 'column' }}>
                <Header />
                <Paper elevation={2} sx={{ p: 3, mb: 3 }}>
                    <Typography variant="h5" gutterBottom>
                        Upload Phone Numbers
                    </Typography>

                    <Stack spacing={2}>
                        <Box>
                            <Typography paragraph>
                                Upload a file or paste phone numbers to split them into manageable chunks. We support various formats including CSV,
                                Excel, and text files. Numbers can be in international format (+263771234567) or local format (0771234567).
                                <Link href="/how-it-works" sx={{ ml: 1 }} underline="hover">
                                    Learn more about how it works
                                </Link>
                            </Typography>
                            <Button
                                variant="outlined"
                                href="/samples/sample_numbers.csv"
                                download
                                size="small"
                                startIcon={<CloudDownloadIcon />}
                                sx={{ mt: 1 }}
                            >
                                Download Sample CSV
                            </Button>
                        </Box>
                    </Stack>
                </Paper>

                {loadingCountries && (
                    <Box sx={{ width: '100%', mb: 2 }}>
                        <LinearProgress />
                    </Box>
                )}
                <Paper elevation={2} sx={{ opacity: loadingCountries ? 0.7 : 1, transition: 'opacity 0.2s', mb: 4 }}>
                    <Box component="form" onSubmit={handleSubmit} sx={{ p: 3 }}>
                        <Box sx={{ display: 'flex', alignItems: 'center', mb: 3 }}>
                            <Typography variant="h4" sx={{ flex: 1 }}>
                                <Box sx={{ display: 'flex', alignItems: 'center' }}>
                                    <CloudUploadIcon sx={{ mr: 1 }} />
                                    Upload Phone Numbers
                                </Box>
                            </Typography>
                            {loadingCountries && (
                                <Typography variant="body2" color="text.secondary">
                                    Loading countries...
                                </Typography>
                            )}
                        </Box>

                        {error && (
                            <Alert severity="error" sx={{ mb: 3 }}>
                                <AlertTitle>Error</AlertTitle>
                                {error}
                            </Alert>
                        )}

                        <Stack spacing={3}>
                            <Box>
                                <Button variant="outlined" component="label" fullWidth disabled={processing || loadingCountries}>
                                    <Box sx={{ display: 'flex', alignItems: 'center' }}>
                                        <UploadIcon sx={{ mr: 1 }} />
                                        Upload File (CSV, Excel, or Text)
                                    </Box>
                                    <input
                                        type="file"
                                        hidden
                                        accept=".csv,.xlsx,.xls,.txt"
                                        onChange={(e: React.ChangeEvent<HTMLInputElement>) => {
                                            const files = e.target.files;
                                            if (files?.length) {
                                                setFile(files[0]);
                                                setTextContent('');
                                            }
                                        }}
                                    />
                                </Button>
                                {file && (
                                    <Typography variant="body2" sx={{ mt: 1, color: 'text.secondary' }}>
                                        Selected file: {file.name}
                                    </Typography>
                                )}
                            </Box>

                            <TextField
                                label="Or Paste Numbers (one per line)"
                                multiline
                                rows={4}
                                value={textContent}
                                onChange={(e) => {
                                    setTextContent(e.target.value);
                                    setFile(null);
                                }}
                                disabled={processing || loadingCountries}
                                placeholder="Enter phone numbers here..."
                                fullWidth
                            />

                            <FormControl fullWidth>
                                <InputLabel id="country-code-label">
                                    <Box sx={{ display: 'flex', alignItems: 'center' }}>
                                        <PublicIcon sx={{ mr: 1, fontSize: '1.2rem' }} />
                                        Country Code
                                    </Box>
                                </InputLabel>
                                <Select
                                    labelId="country-code-label"
                                    value={countryCode}
                                    label={
                                        <Box sx={{ display: 'flex', alignItems: 'center' }}>
                                            <PublicIcon sx={{ mr: 1, fontSize: '1.2rem' }} />
                                            Country Code
                                        </Box>
                                    }
                                    onChange={(e) => setCountryCode(e.target.value)}
                                    disabled={processing || loadingCountries}
                                    sx={{ opacity: loadingCountries ? 0.7 : 1 }}
                                >
                                    {loadingCountries ? (
                                        <MenuItem disabled>Loading countries...</MenuItem>
                                    ) : (
                                        countries.map((country) => (
                                            <MenuItem key={country.code} value={country.code}>
                                                {country.name} ({country.prefix})
                                            </MenuItem>
                                        ))
                                    )}
                                </Select>
                            </FormControl>

                            <TextField
                                label={
                                    <Box sx={{ display: 'flex', alignItems: 'center' }}>
                                        <NumbersIcon sx={{ mr: 1, fontSize: '1.2rem' }} />
                                        Chunk Size
                                    </Box>
                                }
                                type="number"
                                value={chunkSize}
                                onChange={(e) => setChunkSize(parseInt(e.target.value))}
                                disabled={processing || loadingCountries}
                                inputProps={{ min: 100, max: 10000 }}
                                fullWidth
                            />

                            <Button type="submit" variant="contained" disabled={processing || loadingCountries} fullWidth size="large">
                                <Box sx={{ display: 'flex', alignItems: 'center' }}>
                                    <CloudUploadIcon sx={{ mr: 1 }} />
                                    {processing ? 'Processing...' : 'Upload and Process'}
                                </Box>
                            </Button>
                        </Stack>
                    </Box>
                </Paper>
                <Footer />
            </Container>
        </>
    );
};

export default UploadPage;
