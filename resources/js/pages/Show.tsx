import React from 'react';
import { Head } from '@inertiajs/react';
import {
  Box,
  Button,
  Container,
  Grid,
  Paper,
  Stack,
  Typography,
  useTheme,
} from '@mui/material';
import {
  ArrowBack as ArrowBackIcon,
  CloudDownload as CloudDownloadIcon,
  Numbers as NumbersIcon,
  CheckCircle as CheckCircleIcon,
  Error as ErrorIcon,
} from '@mui/icons-material';

import { UploadData } from '@/types/api';
import Footer from '@/components/Footer';
import Header from '@/components/Header';

interface Props {
  upload: UploadData;
}

const UploadDetailsPage: React.FC<Props> = ({ upload }) => {
  const theme = useTheme();

  const expiresIn = () => {
    const expires = new Date(upload.expires_at);
    const now = new Date();
    const days = Math.ceil((expires.getTime() - now.getTime()) / (1000 * 60 * 60 * 24));
    return `${days} days`;
  };

  return (
    <>
      <Head title="Upload Details - SMS X Numbers" />
      <Container maxWidth="lg" sx={{ py: 4, minHeight: '100vh', display: 'flex', flexDirection: 'column' }}>
        <Header />
        <Paper elevation={2}>
          <Box sx={{ p: 3 }}>
            <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 4 }}>
              <Box>
                <Button
                  variant="outlined"
                  href="/"
                  sx={{ mr: 2 }}
                  startIcon={<ArrowBackIcon />}
                >
                  Back to Home
                </Button>
              </Box>
              <Typography variant="h4">Upload Details</Typography>
              <Typography variant="body2" color="text.secondary">
                Expires in: {expiresIn()}
              </Typography>
            </Box>

            <Stack spacing={4}>
              <Box>
                <Typography variant="h5" gutterBottom>
                  Original File
                </Typography>
                <Paper variant="outlined" sx={{ p: 2 }}>
                  <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                    <Typography>{upload.original_file.name}</Typography>
                    <Button
                      variant="contained"
                      component="a"
                      href={upload.original_file.download_url}
                      download={upload.original_file.name}
                      startIcon={<CloudDownloadIcon />}
                    >
                      Download Original
                    </Button>
                  </Box>
                </Paper>
              </Box>

              <Box>
                <Typography variant="h5" gutterBottom>
                  Processing Results
                </Typography>
                <Grid container spacing={2}>
                  <Grid size={4}>
                    <Paper
                      variant="outlined"
                      sx={{ p: 2, textAlign: 'center' }}
                    >
                      <Box sx={{ display: 'flex', alignItems: 'center', justifyContent: 'center', mb: 1 }}>
                        <NumbersIcon sx={{ mr: 1 }} />
                        <Typography variant="body2" color="text.secondary">
                          Total Numbers
                        </Typography>
                      </Box>
                      <Typography variant="h4">{upload.stats.total}</Typography>
                    </Paper>
                  </Grid>
                  <Grid size={4}>
                    <Paper
                      variant="outlined"
                      sx={{ p: 2, textAlign: 'center', bgcolor: theme.palette.success.light }}
                    >
                      <Box sx={{ display: 'flex', alignItems: 'center', justifyContent: 'center', mb: 1 }}>
                        <CheckCircleIcon color="success" sx={{ mr: 1 }} />
                        <Typography variant="body2" color="text.secondary">
                          Valid Numbers
                        </Typography>
                      </Box>
                      <Typography variant="h4" color="success.dark">
                        {upload.stats.valid}
                      </Typography>
                    </Paper>
                  </Grid>
                  <Grid size={4}>
                    <Paper
                      variant="outlined"
                      sx={{ p: 2, textAlign: 'center', bgcolor: theme.palette.error.light }}
                    >
                      <Box sx={{ display: 'flex', alignItems: 'center', justifyContent: 'center', mb: 1 }}>
                        <ErrorIcon color="error" sx={{ mr: 1 }} />
                        <Typography variant="body2" color="text.secondary">
                          Invalid Numbers
                        </Typography>
                      </Box>
                      <Typography variant="h4" color="error.dark">
                        {upload.stats.invalid}
                      </Typography>
                    </Paper>
                  </Grid>
                </Grid>
              </Box>

              <Box>
                <Typography variant="h5" gutterBottom>
                  Processed Chunks
                </Typography>
                <Stack spacing={2}>
                  {upload.chunks.map((chunk) => (
                    <Paper
                      key={chunk.id}
                      variant="outlined"
                      sx={{ p: 2 }}
                    >
                      <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                        <Box>
                          <Typography>Chunk {chunk.index}</Typography>
                          <Typography variant="body2" color="text.secondary">
                            {chunk.size} numbers
                          </Typography>
                        </Box>
                        <Button
                          variant="outlined"
                          component="a"
                          href={chunk.download_url}
                          download={`chunk_${upload.id}_${chunk.index}.csv`}
                          startIcon={<CloudDownloadIcon />}
                        >
                          Download Chunk
                        </Button>
                      </Box>
                    </Paper>
                  ))}
                </Stack>
              </Box>
            </Stack>
          </Box>
        </Paper>
        <Footer />
      </Container>
    </>
  );
};

export default UploadDetailsPage;
